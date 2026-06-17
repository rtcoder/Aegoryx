<?php

namespace App\Modules\Identity\Actions;

use App\Models\Landlord\AuditLog;
use App\Models\Landlord\Identity;
use App\Modules\Audit\Enums\AuditLogAction;
use Illuminate\Support\Facades\DB;

final readonly class DisableTwoFactorAuthAction
{
    public function handle(
        Identity $identity,
        Identity $actor,
        ?string $ip,
        ?string $userAgent,
    ): Identity {
        return DB::transaction(function () use ($identity, $actor, $ip, $userAgent): Identity {
            $wasEnabled = $identity->hasTwoFactorEnabled();

            $identity->forceFill([
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
                'two_factor_confirmed_at' => null,
                'updated_by' => $actor->id,
            ])->save();

            AuditLog::query()->create([
                'actor_type' => 'superadmin',
                'actor_id' => $actor->id,
                'subject_type' => Identity::class,
                'subject_id' => $identity->id,
                'action' => AuditLogAction::TwoFactorDisabled,
                'description' => __('audit.two_factor_disabled', ['identity' => $identity->email]),
                'before_json' => [
                    'two_factor_enabled' => $wasEnabled,
                ],
                'after_json' => [
                    'two_factor_enabled' => false,
                ],
                'metadata_json' => [
                    'identity_email' => $identity->email,
                ],
                'ip' => $ip,
                'user_agent' => $userAgent,
            ]);

            return $identity->refresh();
        });
    }
}
