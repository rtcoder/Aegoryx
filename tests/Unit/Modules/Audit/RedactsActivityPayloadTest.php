<?php

namespace Tests\Unit\Modules\Audit;

use App\Modules\Audit\Services\RedactsActivityPayload;
use Tests\TestCase;

final class RedactsActivityPayloadTest extends TestCase
{
    public function test_it_redacts_nested_sensitive_payload_values(): void
    {
        $payload = [
            'name' => 'Visible',
            'email' => 'ada@example.test',
            'nested' => [
                'api_key' => 'secret-api-key',
                'payment_token' => 'secret-token',
                'safe' => 'kept',
            ],
        ];

        $redacted = app(RedactsActivityPayload::class)->redact($payload);

        $this->assertSame('Visible', $redacted['name']);
        $this->assertSame('[redacted]', $redacted['email']);
        $this->assertSame('[redacted]', $redacted['nested']['api_key']);
        $this->assertSame('[redacted]', $redacted['nested']['payment_token']);
        $this->assertSame('kept', $redacted['nested']['safe']);
    }
}
