<?php

namespace App\Modules\Crm\Policies;

use App\Models\Tenant\CrmTask;
use App\Models\Tenant\User;

final class CrmTaskPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->exists;
    }

    public function view(User $user, CrmTask $task): bool
    {
        return $user->exists && $task->exists;
    }

    public function create(User $user): bool
    {
        return $user->exists && $user->canManageTenantCrm();
    }

    public function update(User $user, CrmTask $task): bool
    {
        return $user->exists && $task->exists && $user->canManageTenantCrm();
    }

    public function delete(User $user, CrmTask $task): bool
    {
        return $user->exists && $task->exists && $user->canManageTenantCrm();
    }
}
