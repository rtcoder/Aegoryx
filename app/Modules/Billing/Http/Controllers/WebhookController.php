<?php

namespace App\Modules\Billing\Http\Controllers;

use App\Modules\Billing\Actions\SyncSubscriptionFromProviderEventAction;
use App\Modules\Billing\Enums\BillingProvider;
use App\Modules\Billing\Support\WebhookSignatureVerifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;

final class WebhookController extends Controller
{
    public function __invoke(
        Request $request,
        string $provider,
        WebhookSignatureVerifier $signatureVerifier,
        SyncSubscriptionFromProviderEventAction $syncSubscription,
    ): JsonResponse {
        $billingProvider = BillingProvider::tryFrom($provider);
        abort_unless($billingProvider instanceof BillingProvider, 404);

        abort_unless(
            $signatureVerifier->verify(
                provider: $billingProvider,
                payload: $request->getContent(),
                signature: $request->header('X-Aegoryx-Signature'),
            ),
            401,
        );

        $payload = $request->validate([
            'event_id' => ['required', 'string', 'max:255'],
            'event_type' => ['required', 'string', 'max:255'],
            'tenant_id' => ['required', 'integer', 'exists:tenants,id'],
            'plan_id' => ['nullable', 'integer', 'exists:plans,id'],
            'provider_subscription_id' => ['required', 'string', 'max:255'],
            'provider_status' => ['required', 'string', 'max:255'],
            'provider' => ['nullable', Rule::in([$billingProvider->value])],
        ]);

        $subscription = $syncSubscription->handle(
            provider: $billingProvider,
            providerEventId: $payload['event_id'],
            eventType: $payload['event_type'],
            tenantId: (int) $payload['tenant_id'],
            planId: isset($payload['plan_id']) ? (int) $payload['plan_id'] : null,
            providerSubscriptionId: $payload['provider_subscription_id'],
            providerStatus: $payload['provider_status'],
            payload: $request->all(),
            ip: $request->ip(),
            userAgent: $request->userAgent(),
        );

        return response()->json([
            'status' => 'processed',
            'subscription_id' => $subscription->id,
            'subscription_status' => $subscription->status->value,
        ]);
    }
}
