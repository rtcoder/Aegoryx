<?php

namespace Tests\Feature\Billing;

use App\Models\Landlord\AuditLog;
use App\Models\Landlord\BillingEvent;
use App\Models\Landlord\Plan;
use App\Models\Landlord\Subscription;
use App\Models\Landlord\Tenant;
use App\Modules\Audit\Enums\AuditLogAction;
use App\Modules\Billing\Actions\SyncSubscriptionFromProviderEventAction;
use App\Modules\Billing\Enums\BillingEventStatus;
use App\Modules\Billing\Enums\BillingProvider;
use App\Modules\Billing\Enums\PlanStatus;
use App\Modules\Billing\Enums\SubscriptionStatus;
use App\Modules\Tenancy\Enums\TenantBillingModel;
use App\Modules\Tenancy\Enums\TenantDeploymentType;
use App\Modules\Tenancy\Enums\TenantLicenseType;
use App\Modules\Tenancy\Enums\TenantStatus;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class SubscriptionMappingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh', [
            '--database' => 'sqlite',
            '--path' => 'database/migrations/landlord',
        ]);
    }

    public function test_provider_event_creates_subscription_billing_event_and_audit_log(): void
    {
        $tenant = $this->tenant();
        $plan = Plan::query()->create([
            'key' => 'growth',
            'name' => 'Growth',
            'status' => PlanStatus::Active,
        ]);

        $subscription = app(SyncSubscriptionFromProviderEventAction::class)->handle(
            provider: BillingProvider::Paddle,
            providerEventId: 'evt_001',
            eventType: 'subscription.updated',
            tenantId: $tenant->id,
            planId: $plan->id,
            providerSubscriptionId: 'sub_001',
            providerStatus: 'active',
            payload: ['provider_status' => 'active'],
            actorId: null,
            ip: '127.0.0.1',
            userAgent: 'Webhook Test',
        );

        $event = BillingEvent::query()->firstOrFail();
        $auditLog = AuditLog::query()->where('action', AuditLogAction::BillingSubscriptionSynced)->firstOrFail();

        $this->assertSame(SubscriptionStatus::Active, $subscription->status);
        $this->assertSame($tenant->id, $subscription->tenant_id);
        $this->assertSame($plan->id, $subscription->plan_id);
        $this->assertSame(BillingEventStatus::Processed, $event->status);
        $this->assertSame($subscription->id, $event->subscription_id);
        $this->assertSame('evt_001', $auditLog->metadata_json['provider_event_id']);
        $this->assertSame(SubscriptionStatus::Active->value, $auditLog->after_json['status']);
    }

    public function test_duplicate_provider_event_is_idempotent_and_does_not_duplicate_audit(): void
    {
        $tenant = $this->tenant();
        $action = app(SyncSubscriptionFromProviderEventAction::class);

        $first = $action->handle(
            provider: BillingProvider::Paddle,
            providerEventId: 'evt_002',
            eventType: 'subscription.updated',
            tenantId: $tenant->id,
            planId: null,
            providerSubscriptionId: 'sub_002',
            providerStatus: 'trialing',
        );

        $second = $action->handle(
            provider: BillingProvider::Paddle,
            providerEventId: 'evt_002',
            eventType: 'subscription.updated',
            tenantId: $tenant->id,
            planId: null,
            providerSubscriptionId: 'sub_002',
            providerStatus: 'cancelled',
        );

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, Subscription::query()->count());
        $this->assertSame(1, BillingEvent::query()->count());
        $this->assertSame(BillingEventStatus::Duplicate, BillingEvent::query()->firstOrFail()->status);
        $this->assertSame(1, AuditLog::query()->where('action', AuditLogAction::BillingSubscriptionSynced)->count());
        $this->assertSame(SubscriptionStatus::Trialing, $second->refresh()->status);
    }

    /**
     * @return array<string, array{0: string, 1: SubscriptionStatus}>
     */
    public static function providerStatuses(): array
    {
        return [
            'active' => ['active', SubscriptionStatus::Active],
            'trial' => ['trialing', SubscriptionStatus::Trialing],
            'payment failed' => ['payment_failed', SubscriptionStatus::PastDue],
            'cancelled' => ['cancelled', SubscriptionStatus::Cancelled],
            'unknown' => ['whatever', SubscriptionStatus::Inactive],
        ];
    }

    #[DataProvider('providerStatuses')]
    public function test_provider_statuses_map_to_internal_statuses(string $providerStatus, SubscriptionStatus $expected): void
    {
        $tenant = $this->tenant();

        $subscription = app(SyncSubscriptionFromProviderEventAction::class)->handle(
            provider: BillingProvider::Paddle,
            providerEventId: 'evt_'.$providerStatus,
            eventType: 'subscription.updated',
            tenantId: $tenant->id,
            planId: null,
            providerSubscriptionId: 'sub_'.$providerStatus,
            providerStatus: $providerStatus,
        );

        $this->assertSame($expected, $subscription->status);
    }

    public function test_signed_billing_webhook_syncs_subscription(): void
    {
        config()->set('aegoryx.billing.webhooks.paddle_secret', 'test-secret');

        $tenant = $this->tenant();
        $plan = Plan::query()->create([
            'key' => 'growth',
            'name' => 'Growth',
            'status' => PlanStatus::Active,
        ]);
        $payload = [
            'event_id' => 'evt_http_001',
            'event_type' => 'subscription.updated',
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'provider_subscription_id' => 'sub_http_001',
            'provider_status' => 'active',
        ];

        $this
            ->postJson(
                '/api/billing/webhooks/paddle',
                $payload,
                ['X-Aegoryx-Signature' => $this->signature($payload)],
            )
            ->assertOk()
            ->assertJson([
                'status' => 'processed',
                'subscription_status' => SubscriptionStatus::Active->value,
            ]);

        $this->assertSame(1, Subscription::query()->count());
        $this->assertSame(1, BillingEvent::query()->count());
        $this->assertSame(1, AuditLog::query()->where('action', AuditLogAction::BillingSubscriptionSynced)->count());
    }

    public function test_billing_webhook_rejects_invalid_signature(): void
    {
        config()->set('aegoryx.billing.webhooks.paddle_secret', 'test-secret');

        $tenant = $this->tenant();

        $this
            ->postJson('/api/billing/webhooks/paddle', [
                'event_id' => 'evt_http_002',
                'event_type' => 'subscription.updated',
                'tenant_id' => $tenant->id,
                'provider_subscription_id' => 'sub_http_002',
                'provider_status' => 'active',
            ], ['X-Aegoryx-Signature' => 'invalid'])
            ->assertUnauthorized();

        $this->assertSame(0, Subscription::query()->count());
        $this->assertSame(0, BillingEvent::query()->count());
    }

    public function test_billing_webhook_retry_is_idempotent(): void
    {
        config()->set('aegoryx.billing.webhooks.paddle_secret', 'test-secret');

        $tenant = $this->tenant();
        $payload = [
            'event_id' => 'evt_http_003',
            'event_type' => 'subscription.updated',
            'tenant_id' => $tenant->id,
            'provider_subscription_id' => 'sub_http_003',
            'provider_status' => 'trialing',
        ];
        $signature = $this->signature($payload);

        $this
            ->postJson('/api/billing/webhooks/paddle', $payload, ['X-Aegoryx-Signature' => $signature])
            ->assertOk();

        $this
            ->postJson('/api/billing/webhooks/paddle', $payload, ['X-Aegoryx-Signature' => $signature])
            ->assertOk();

        $this->assertSame(1, Subscription::query()->count());
        $this->assertSame(1, BillingEvent::query()->count());
        $this->assertSame(BillingEventStatus::Duplicate, BillingEvent::query()->firstOrFail()->status);
        $this->assertSame(1, AuditLog::query()->where('action', AuditLogAction::BillingSubscriptionSynced)->count());
    }

    public function test_failed_billing_event_can_be_retried_from_stored_payload(): void
    {
        $tenant = $this->tenant();

        $event = BillingEvent::query()->create([
            'provider' => BillingProvider::Paddle,
            'provider_event_id' => 'evt_retry_action_001',
            'event_type' => 'subscription.updated',
            'tenant_id' => $tenant->id,
            'status' => BillingEventStatus::Failed,
            'payload' => [
                'tenant_id' => $tenant->id,
                'provider_subscription_id' => 'sub_retry_action_001',
                'provider_status' => 'active',
            ],
            'failure_reason' => 'Provider timeout.',
            'failed_at' => now(),
        ]);

        $subscription = app(SyncSubscriptionFromProviderEventAction::class)->retry($event);

        $event->refresh();

        $this->assertSame(SubscriptionStatus::Active, $subscription->status);
        $this->assertSame(BillingEventStatus::Processed, $event->status);
        $this->assertSame($subscription->id, $event->subscription_id);
        $this->assertNull($event->failure_reason);
        $this->assertNull($event->failed_at);
        $this->assertNotNull($event->processed_at);
        $this->assertSame(1, AuditLog::query()->where('action', AuditLogAction::BillingSubscriptionSynced)->count());
    }

    private function tenant(): Tenant
    {
        return Tenant::query()->create([
            'name' => 'Acme Tenant',
            'slug' => fake()->unique()->slug(),
            'schema_name' => 'tenant_'.fake()->unique()->word(),
            'status' => TenantStatus::Active,
            'deployment_type' => TenantDeploymentType::Saas,
            'billing_model' => TenantBillingModel::Subscription,
            'license_type' => TenantLicenseType::SaasSubscription,
        ]);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function signature(array $payload): string
    {
        return hash_hmac('sha256', json_encode($payload, JSON_THROW_ON_ERROR), 'test-secret');
    }
}
