<?php

namespace App\Modules\Crm\Policies;

use App\Models\Tenant\CrmContact;
use App\Models\Tenant\User;

final class CrmContactPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->exists;
    }

    public function view(User $user, CrmContact $contact): bool
    {
        return $user->exists && $contact->exists;
    }

    public function create(User $user): bool
    {
        return $user->exists && $user->canManageTenantCrm();
    }

    public function update(User $user, CrmContact $contact): bool
    {
        return $user->exists && $contact->exists && $user->canManageTenantCrm();
    }

    public function delete(User $user, CrmContact $contact): bool
    {
        return $user->exists && $contact->exists && $user->canManageTenantCrm();
    }
}
