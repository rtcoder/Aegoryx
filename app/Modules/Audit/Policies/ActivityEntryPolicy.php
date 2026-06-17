<?php

namespace App\Modules\Audit\Policies;

use App\Models\Tenant\ActivityEntry;
use App\Models\Tenant\User;

final class ActivityEntryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->exists && $user->canExportTenantActivity();
    }

    public function view(User $user, ActivityEntry $entry): bool
    {
        return $entry->exists && $this->viewAny($user);
    }

    public function export(User $user): bool
    {
        return $user->exists && $user->canExportTenantActivity();
    }
}
