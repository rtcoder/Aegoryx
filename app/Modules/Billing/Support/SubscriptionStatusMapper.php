<?php

namespace App\Modules\Billing\Support;

use App\Modules\Billing\Enums\SubscriptionStatus;

final readonly class SubscriptionStatusMapper
{
    public function fromProviderStatus(string $providerStatus): SubscriptionStatus
    {
        return match (strtolower($providerStatus)) {
            'trialing', 'trial' => SubscriptionStatus::Trialing,
            'active' => SubscriptionStatus::Active,
            'past_due', 'past-due', 'payment_failed', 'paused' => SubscriptionStatus::PastDue,
            'cancelled', 'canceled', 'deleted' => SubscriptionStatus::Cancelled,
            default => SubscriptionStatus::Inactive,
        };
    }
}
