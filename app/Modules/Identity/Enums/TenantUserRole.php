<?php

namespace App\Modules\Identity\Enums;

enum TenantUserRole: string
{
    case Owner = 'owner';
    case Admin = 'admin';
    case Member = 'member';
    case Viewer = 'viewer';

    public function canManageUsers(): bool
    {
        return in_array($this, [self::Owner, self::Admin], true);
    }

    public function canManageContent(): bool
    {
        return in_array($this, [self::Owner, self::Admin, self::Member], true);
    }

    public function canManageCrm(): bool
    {
        return in_array($this, [self::Owner, self::Admin, self::Member], true);
    }

    public function canManageFiles(): bool
    {
        return in_array($this, [self::Owner, self::Admin, self::Member], true);
    }

    public function canExportActivity(): bool
    {
        return in_array($this, [self::Owner, self::Admin], true);
    }

    public function canManageSettings(): bool
    {
        return in_array($this, [self::Owner, self::Admin], true);
    }
}
