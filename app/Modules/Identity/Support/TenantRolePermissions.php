<?php

namespace App\Modules\Identity\Support;

use App\Modules\Identity\Enums\TenantPermission;
use App\Modules\Identity\Enums\TenantUserRole;

final readonly class TenantRolePermissions
{
    /**
     * @return array<int, TenantPermission>
     */
    public function permissionsFor(TenantUserRole $role): array
    {
        return match ($role) {
            TenantUserRole::Owner => TenantPermission::cases(),
            TenantUserRole::Admin => [
                TenantPermission::ManageUsers,
                TenantPermission::ManageContent,
                TenantPermission::PublishContent,
                TenantPermission::ManageCrm,
                TenantPermission::ManageFiles,
                TenantPermission::ExportActivity,
                TenantPermission::ManageSettings,
            ],
            TenantUserRole::Member => [
                TenantPermission::ManageContent,
                TenantPermission::PublishContent,
                TenantPermission::ManageCrm,
                TenantPermission::ManageFiles,
            ],
            TenantUserRole::Viewer => [],
        };
    }

    public function roleHas(TenantUserRole $role, TenantPermission $permission): bool
    {
        return in_array($permission, $this->permissionsFor($role), true);
    }
}
