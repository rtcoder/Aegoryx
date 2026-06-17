<?php

namespace App\Modules\Identity\Actions;

use App\Models\Tenant\User;
use App\Modules\Audit\Enums\ActivityEntryAction;
use App\Modules\Audit\Services\ActivityLogger;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

final readonly class DisableTenantTwoFactorAuthAction
{
    public function __construct(
        private ActivityLogger $activity,
    ) {}

    /**
     * @throws AuthorizationException
     */
    public function handle(
        User $user,
        User $actor,
        ?string $ip,
        ?string $userAgent,
    ): User {
        $this->authorize($user, $actor);

        return DB::transaction(function () use ($user, $actor, $ip, $userAgent): User {
            $wasEnabled = $user->hasTwoFactorEnabled();

            $user->forceFill([
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
                'two_factor_confirmed_at' => null,
                'updated_by' => $actor->id,
            ])->save();

            $this->activity->record(
                actorType: User::class,
                actorId: $actor->id,
                subject: $user,
                action: ActivityEntryAction::TenantTwoFactorDisabled,
                description: __('activity.tenant_two_factor_disabled', ['user' => $user->email]),
                before: [
                    'two_factor_enabled' => $wasEnabled,
                ],
                after: [
                    'two_factor_enabled' => false,
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
