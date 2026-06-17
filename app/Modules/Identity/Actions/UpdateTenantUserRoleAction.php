<?php

namespace App\Modules\Identity\Actions;

use App\Models\Tenant\User;
use App\Modules\Identity\Enums\TenantUserRole;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;

final readonly class UpdateTenantUserRoleAction
{
    /**
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function handle(User $user, TenantUserRole $role, User $actor): User
    {
        if (! $actor->canManageTenantUsers()) {
            throw new AuthorizationException;
        }

        if ($user->role === TenantUserRole::Owner && $role !== TenantUserRole::Owner && $this->isLastOwner($user)) {
            throw ValidationException::withMessages([
                'role' => __('tenant_panel.users.last_owner_error'),
            ]);
        }

        $user->forceFill([
            'role' => $role,
            'updated_by' => $actor->id,
        ])->save();

        return $user->refresh();
    }

    private function isLastOwner(User $user): bool
    {
        return User::query()
            ->where('role', TenantUserRole::Owner->value)
            ->whereKeyNot($user->id)
            ->doesntExist();
    }
}
