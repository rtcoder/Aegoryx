<?php

namespace App\Modules\Crm\Policies;

use App\Models\Tenant\CrmDeal;
use App\Models\Tenant\User;

final class CrmDealPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->exists;
    }

    public function view(User $user, CrmDeal $deal): bool
    {
        return $user->exists && $deal->exists;
    }

    public function create(User $user): bool
    {
        return $user->exists;
    }

    public function update(User $user, CrmDeal $deal): bool
    {
        return $user->exists && $deal->exists;
    }

    public function delete(User $user, CrmDeal $deal): bool
    {
        return $user->exists && $deal->exists;
    }
}
