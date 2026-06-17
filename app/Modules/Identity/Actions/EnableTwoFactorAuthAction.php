<?php

namespace App\Modules\Identity\Actions;

use App\Models\Landlord\AuditLog;
use App\Models\Landlord\Identity;
use App\Modules\Audit\Enums\AuditLogAction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final readonly class EnableTwoFactorAuthAction
{
    /**
     * @param  list<string>  $recoveryCodes
     */
    public function handle(
        Identity $identity,
        string $secret,
        array $recoveryCodes,
        Identity $actor,
        ?string $ip,
        ?string $userAgent,
    ): Identity {
        return DB::transaction(function () use ($identity, $secret, $recoveryCodes, $actor, $ip, $userAgent): Identity {
            $identity->forceFill([
                'two_factor_secret' => $secret,
                'two_factor_recovery_codes' => array_map(
                    static fn (string $code): string => Hash::make($code),
                    $recoveryCodes,
                ),
                'two_factor_confirmed_at' => now(),
                'updated_by' => $actor->id,
            ])->save();

            AuditLog::query()->create([
                'actor_type' => 'superadmin',
                'actor_id' => $actor->id,
                'subject_type' => Identity::class,
                'subject_id' => $identity->id,
                'action' => AuditLogAction::TwoFactorEnabled,
                'description' => __('audit.two_factor_enabled', ['identity' => $identity->email]),
                'before_json' => null,
                'after_json' => [
                    'two_factor_enabled' => true,
                    'recovery_code_count' => count($recoveryCodes),
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
