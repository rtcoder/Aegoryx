<?php

namespace App\Modules\Files\Policies;

use App\Models\Tenant\TenantFile;
use App\Models\Tenant\User;

final class TenantFilePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->exists;
    }

    public function view(User $user, TenantFile $file): bool
    {
        return $user->exists && $file->exists && $this->ownedByUser($user, $file);
    }

    public function create(User $user): bool
    {
        return $user->exists;
    }

    public function delete(User $user, TenantFile $file): bool
    {
        return $user->exists && $file->exists && $this->ownedByUser($user, $file);
    }

    public function download(User $user, TenantFile $file): bool
    {
        return $this->view($user, $file);
    }

    private function ownedByUser(User $user, TenantFile $file): bool
    {
        return $file->owner_id === null || $file->owner_id === $user->id;
    }
}
