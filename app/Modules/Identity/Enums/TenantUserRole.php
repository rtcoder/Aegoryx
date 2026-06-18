<?php

namespace App\Modules\Identity\Enums;

use App\Modules\Identity\Support\TenantRolePermissions;

enum TenantUserRole: string
{
    case Owner = 'owner';
    case Admin = 'admin';
    case Member = 'member';
    case Viewer = 'viewer';

    public function canManageUsers(): bool
    {
        return $this->hasPermission(TenantPermission::ManageUsers);
    }

    public function canManageContent(): bool
    {
        return $this->hasPermission(TenantPermission::ManageContent);
    }

    public function canPublishContent(): bool
    {
        return $this->hasPermission(TenantPermission::PublishContent);
    }

    public function canManageCrm(): bool
    {
        return $this->hasPermission(TenantPermission::ManageCrm);
    }

    public function canManageFiles(): bool
    {
        return $this->hasPermission(TenantPermission::ManageFiles);
    }

    public function canExportActivity(): bool
    {
        return $this->hasPermission(TenantPermission::ExportActivity);
    }

    public function canManageSettings(): bool
    {
        return $this->hasPermission(TenantPermission::ManageSettings);
    }

    public function hasPermission(TenantPermission $permission): bool
    {
        return (new TenantRolePermissions)->roleHas($this, $permission);
    }
}
