<?php

namespace App\Modules\Billing\Support;

use App\Modules\Billing\Enums\BillingProvider;

final readonly class WebhookSignatureVerifier
{
    public function verify(BillingProvider $provider, string $payload, ?string $signature): bool
    {
        $secret = (string) config("aegoryx.billing.webhooks.{$provider->value}_secret", '');

        if ($secret === '' || $signature === null || $signature === '') {
            return false;
        }

        $expected = hash_hmac('sha256', $payload, $secret);

        return hash_equals($expected, $signature);
    }
}
