<?php

namespace App\Modules\Crm\Policies;

use App\Models\Tenant\CrmNote;
use App\Models\Tenant\User;

final class CrmNotePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->exists;
    }

    public function view(User $user, CrmNote $note): bool
    {
        return $user->exists && $note->exists;
    }

    public function create(User $user): bool
    {
        return $user->exists && $user->canManageTenantCrm();
    }

    public function update(User $user, CrmNote $note): bool
    {
        return $user->exists && $note->exists && $user->canManageTenantCrm();
    }

    public function delete(User $user, CrmNote $note): bool
    {
        return $user->exists && $note->exists && $user->canManageTenantCrm();
    }
}
