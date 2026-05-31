<?php

namespace App\Modules\Licensing\Actions;

use App\Models\Landlord\AuditLog;
use App\Models\Landlord\Identity;
use App\Models\Landlord\License;
use App\Modules\Licensing\Enums\LicenseStatus;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

final readonly class VerifyLicenseAction
{
    public function handle(
        License $license,
        Identity $actor,
        ?string $ip,
        ?string $userAgent,
    ): License {
        return DB::transaction(function () use ($license, $actor, $ip, $userAgent): License {
            $before = [
                'status' => $license->status->value,
                'last_verified_at' => $license->last_verified_at?->toISOString(),
            ];

            $status = $this->resolveStatus($license);

            $license->forceFill([
                'status' => $status,
                'last_verified_at' => now(),
                'updated_by' => $actor->id,
            ])->save();

            AuditLog::query()->create([
                'actor_type' => 'superadmin',
                'actor_id' => $actor->id,
                'subject_type' => License::class,
                'subject_id' => $license->id,
                'action' => 'license_verified',
                'description' => "License [{$license->id}] verified as [{$status->value}].",
                'before_json' => $before,
                'after_json' => [
                    'status' => $status->value,
                    'last_verified_at' => $license->last_verified_at?->toISOString(),
                ],
                'metadata_json' => [
                    'tenant_id' => $license->tenant_id,
                    'license_type' => $license->type,
                ],
                'ip' => $ip,
                'user_agent' => $userAgent,
            ]);

            return $license->refresh();
        });
    }

    private function resolveStatus(License $license): LicenseStatus
    {
        if ($license->status === LicenseStatus::Suspended) {
            return LicenseStatus::Suspended;
        }

        if ($license->expires_at === null) {
            return LicenseStatus::Active;
        }

        if ($license->expires_at->isFuture()) {
            return LicenseStatus::Active;
        }

        $graceUntil = $license->payload['grace_until'] ?? null;

        if (is_string($graceUntil)) {
            try {
                if (Carbon::parse($graceUntil)->isFuture()) {
                    return LicenseStatus::Grace;
                }
            } catch (\Throwable) {
                return LicenseStatus::Expired;
            }
        }

        return LicenseStatus::Expired;
    }
}
