<?php

namespace App\Modules\Cms\Policies;

use App\Models\Tenant\CmsPage;
use App\Models\Tenant\User;

final class CmsPagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->exists;
    }

    public function view(User $user, CmsPage $page): bool
    {
        return $user->exists && $page->exists;
    }

    public function create(User $user): bool
    {
        return $user->exists && $user->canManageTenantContent();
    }

    public function update(User $user, CmsPage $page): bool
    {
        return $user->exists && $page->exists && $user->canManageTenantContent();
    }

    public function publish(User $user, CmsPage $page): bool
    {
        return $this->update($user, $page);
    }

    public function unpublish(User $user, CmsPage $page): bool
    {
        return $this->update($user, $page);
    }
}
