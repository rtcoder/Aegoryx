<?php

namespace App\Modules\Billing\Actions;

use App\Models\Landlord\AuditLog;
use App\Models\Landlord\BillingEvent;
use App\Models\Landlord\Subscription;
use App\Modules\Audit\Enums\AuditLogAction;
use App\Modules\Billing\Enums\BillingEventStatus;
use App\Modules\Billing\Enums\BillingProvider;
use App\Modules\Billing\Support\SubscriptionStatusMapper;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class SyncSubscriptionFromProviderEventAction
{
    public function __construct(
        private SubscriptionStatusMapper $statusMapper,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public function handle(
        BillingProvider $provider,
        string $providerEventId,
        string $eventType,
        int $tenantId,
        ?int $planId,
        string $providerSubscriptionId,
        string $providerStatus,
        array $payload = [],
        ?int $actorId = null,
        ?string $ip = null,
        ?string $userAgent = null,
    ): Subscription {
        try {
            return $this->sync(
                provider: $provider,
                providerEventId: $providerEventId,
                eventType: $eventType,
                tenantId: $tenantId,
                planId: $planId,
                providerSubscriptionId: $providerSubscriptionId,
                providerStatus: $providerStatus,
                payload: $payload,
                actorId: $actorId,
                ip: $ip,
                userAgent: $userAgent,
            );
        } catch (Throwable $exception) {
            $this->recordFailure(
                provider: $provider,
                providerEventId: $providerEventId,
                eventType: $eventType,
                tenantId: $tenantId,
                payload: $payload,
                failureReason: $exception->getMessage(),
            );

            throw $exception;
        }
    }

    public function retry(BillingEvent $event, ?int $actorId = null, ?string $ip = null, ?string $userAgent = null): Subscription
    {
        if ($event->status !== BillingEventStatus::Failed) {
            return Subscription::query()->findOrFail($event->subscription_id);
        }

        $payload = $event->payload ?? [];

        $provider = $event->provider instanceof BillingProvider
            ? $event->provider
            : BillingProvider::from($event->provider);

        return $this->sync(
            provider: $provider,
            providerEventId: $event->provider_event_id,
            eventType: $event->event_type,
            tenantId: (int) $payload['tenant_id'],
            planId: isset($payload['plan_id']) ? (int) $payload['plan_id'] : null,
            providerSubscriptionId: (string) $payload['provider_subscription_id'],
            providerStatus: (string) $payload['provider_status'],
            payload: $payload,
            actorId: $actorId,
            ip: $ip,
            userAgent: $userAgent,
            existingFailedEvent: $event,
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function sync(
        BillingProvider $provider,
        string $providerEventId,
        string $eventType,
        int $tenantId,
        ?int $planId,
        string $providerSubscriptionId,
        string $providerStatus,
        array $payload = [],
        ?int $actorId = null,
        ?string $ip = null,
        ?string $userAgent = null,
        ?BillingEvent $existingFailedEvent = null,
    ): Subscription {
        return DB::transaction(function () use ($provider, $providerEventId, $eventType, $tenantId, $planId, $providerSubscriptionId, $providerStatus, $payload, $actorId, $ip, $userAgent, $existingFailedEvent): Subscription {
            $existingEvent = $existingFailedEvent ?? BillingEvent::query()
                ->where('provider', $provider->value)
                ->where('provider_event_id', $providerEventId)
                ->first();

            if ($existingEvent && $existingEvent->status !== BillingEventStatus::Failed) {
                $existingEvent->forceFill([
                    'status' => BillingEventStatus::Duplicate,
                ])->save();

                return Subscription::query()->findOrFail($existingEvent->subscription_id);
            }

            $subscription = Subscription::query()->firstOrNew([
                'provider' => $provider->value,
                'provider_subscription_id' => $providerSubscriptionId,
            ]);

            $before = $subscription->exists ? [
                'plan_id' => $subscription->plan_id,
                'status' => $subscription->status->value,
            ] : null;

            $status = $this->statusMapper->fromProviderStatus($providerStatus);

            $subscription->forceFill([
                'tenant_id' => $tenantId,
                'plan_id' => $planId,
                'status' => $status,
                'provider_payload' => $payload,
                'updated_by' => $actorId,
                'created_by' => $subscription->exists ? $subscription->created_by : $actorId,
            ])->save();

            ($existingEvent ?? new BillingEvent)->forceFill([
                'provider' => $provider->value,
                'provider_event_id' => $providerEventId,
                'event_type' => $eventType,
                'tenant_id' => $tenantId,
                'subscription_id' => $subscription->id,
                'status' => BillingEventStatus::Processed,
                'payload' => $payload,
                'failure_reason' => null,
                'processed_at' => now(),
                'failed_at' => null,
            ])->save();

            AuditLog::query()->create([
                'actor_type' => $actorId === null ? 'system' : 'superadmin',
                'actor_id' => $actorId,
                'subject_type' => Subscription::class,
                'subject_id' => $subscription->id,
                'action' => AuditLogAction::BillingSubscriptionSynced,
                'description' => __('audit.billing_subscription_synced', [
                    'subscription' => $providerSubscriptionId,
                    'status' => $status->value,
                ]),
                'before_json' => $before,
                'after_json' => [
                    'plan_id' => $planId,
                    'status' => $status->value,
                ],
                'metadata_json' => [
                    'provider' => $provider->value,
                    'provider_event_id' => $providerEventId,
                    'event_type' => $eventType,
                    'tenant_id' => $tenantId,
                ],
                'ip' => $ip,
                'user_agent' => $userAgent,
            ]);

            return $subscription->refresh();
        });
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function recordFailure(
        BillingProvider $provider,
        string $providerEventId,
        string $eventType,
        int $tenantId,
        array $payload,
        string $failureReason,
    ): void {
        BillingEvent::query()->updateOrCreate([
            'provider' => $provider->value,
            'provider_event_id' => $providerEventId,
        ], [
            'event_type' => $eventType,
            'tenant_id' => $tenantId,
            'status' => BillingEventStatus::Failed,
            'payload' => $payload,
            'failure_reason' => str($failureReason)->limit(4000)->toString(),
            'failed_at' => now(),
        ]);
    }
}
