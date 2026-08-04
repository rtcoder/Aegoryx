<?php

namespace App\Modules\Crm\Support;

use App\Models\Tenant\User;
use Illuminate\Validation\ValidationException;

final readonly class CrmAssigneeGuard
{
    /**
     * @throws ValidationException
     */
    public function assertAssignable(mixed $userId): void
    {
        if ($userId === null || $userId === '') {
            return;
        }

        $exists = User::query()
            ->whereKey((int) $userId)
            ->whereNull('deleted_at')
            ->exists();

        if (! $exists) {
            throw ValidationException::withMessages([
                'assigned_to' => __('validation.exists', ['attribute' => __('crm.assignee')]),
            ]);
        }
    }
}
