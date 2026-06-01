<?php

namespace App\Modules\Audit\Services;

final readonly class RedactsActivityPayload
{
    /** @var list<string> */
    private const SENSITIVE_KEYS = [
        'api_key',
        'authorization',
        'password',
        'password_confirmation',
        'secret',
        'token',
    ];

    /**
     * @param  array<string, mixed>|null  $payload
     * @return array<string, mixed>|null
     */
    public function redact(?array $payload): ?array
    {
        if ($payload === null) {
            return null;
        }

        return $this->redactArray($payload);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function redactArray(array $payload): array
    {
        foreach ($payload as $key => $value) {
            if ($this->isSensitiveKey((string) $key)) {
                $payload[$key] = '[redacted]';

                continue;
            }

            if (is_array($value)) {
                $payload[$key] = $this->redactArray($value);
            }
        }

        return $payload;
    }

    private function isSensitiveKey(string $key): bool
    {
        $normalized = strtolower($key);

        foreach (self::SENSITIVE_KEYS as $sensitiveKey) {
            if ($normalized === $sensitiveKey || str_ends_with($normalized, '_'.$sensitiveKey)) {
                return true;
            }
        }

        return false;
    }
}
