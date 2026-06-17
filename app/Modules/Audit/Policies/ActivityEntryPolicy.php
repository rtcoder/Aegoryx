<?php

namespace App\Modules\Audit\Policies;

use App\Models\Tenant\User;

final class ActivityEntryPolicy
{
    public function export(User $user): bool
    {
        return $user->exists && $user->canExportTenantActivity();
    }
}
