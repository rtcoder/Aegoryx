<?php

namespace App\Modules\Identity\Actions;

use App\Models\Tenant\User;
use App\Modules\Audit\Enums\ActivityEntryAction;
use App\Modules\Audit\Services\ActivityLogger;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final readonly class EnableTenantTwoFactorAuthAction
{
    public function __construct(
        private ActivityLogger $activity,
    ) {}

    /**
     * @param  list<string>  $recoveryCodes
     *
     * @throws AuthorizationException
     */
    public function handle(
        User $user,
        string $secret,
        array $recoveryCodes,
        User $actor,
        ?string $ip,
        ?string $userAgent,
    ): User {
        $this->authorize($user, $actor);

        return DB::transaction(function () use ($user, $secret, $recoveryCodes, $actor, $ip, $userAgent): User {
            $user->forceFill([
                'two_factor_secret' => $secret,
                'two_factor_recovery_codes' => array_map(
                    static fn (string $code): string => Hash::make($code),
                    $recoveryCodes,
                ),
                'two_factor_confirmed_at' => now(),
                'updated_by' => $actor->id,
            ])->save();

            $this->activity->record(
                actorType: User::class,
                actorId: $actor->id,
                subject: $user,
                action: ActivityEntryAction::TenantTwoFactorEnabled,
                description: __('activity.tenant_two_factor_enabled', ['user' => $user->email]),
                after: [
                    'two_factor_enabled' => true,
                    'recovery_code_count' => count($recoveryCodes),
                ],
                metadata: [
                    'target_user_id' => $user->id,
                ],
                ip: $ip,
                userAgent: $userAgent,
            );

            return $user->refresh();
        });
    }

    /**
     * @throws AuthorizationException
     */
    private function authorize(User $user, User $actor): void
    {
        if ($actor->id === $user->id || $actor->canManageTenantUsers()) {
            return;
        }

        throw new AuthorizationException;
    }
}
