<?php

namespace App\Modules\Identity\Actions;

use App\Models\Tenant\User;
use App\Modules\Identity\Enums\TenantUserRole;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Str;

final readonly class CreateTenantUserAction
{
    /**
     * @return array{user: User, password: string}
     *
     * @throws AuthorizationException
     */
    public function handle(
        string $name,
        string $email,
        TenantUserRole $role,
        User $actor,
        ?string $password = null,
    ): array {
        if (! $actor->canManageTenantUsers()) {
            throw new AuthorizationException;
        }

        $plainPassword = $password ?: Str::password(20);

        $user = User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => $plainPassword,
            'role' => $role,
        ]);

        $user->forceFill([
            'created_by' => $actor->id,
            'updated_by' => $actor->id,
        ])->save();

        return [
            'user' => $user->refresh(),
            'password' => $plainPassword,
        ];
    }
}
