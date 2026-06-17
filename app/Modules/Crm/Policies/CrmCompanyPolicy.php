<?php

namespace App\Modules\Crm\Policies;

use App\Models\Tenant\CrmCompany;
use App\Models\Tenant\User;

final class CrmCompanyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->exists;
    }

    public function view(User $user, CrmCompany $company): bool
    {
        return $user->exists && $company->exists;
    }

    public function create(User $user): bool
    {
        return $user->exists && $user->canManageTenantCrm();
    }

    public function update(User $user, CrmCompany $company): bool
    {
        return $user->exists && $company->exists && $user->canManageTenantCrm();
    }

    public function delete(User $user, CrmCompany $company): bool
    {
        return $user->exists && $company->exists && $user->canManageTenantCrm();
    }
}
