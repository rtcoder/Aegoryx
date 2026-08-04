<?php

namespace App\Modules\Crm\Support;

use App\Modules\Crm\Enums\CrmSubjectType;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final readonly class CrmSubjectGuard
{
    public function exists(CrmSubjectType $type, int $subjectId): bool
    {
        return DB::table($type->table())
            ->where('id', $subjectId)
            ->whereNull('deleted_at')
            ->exists();
    }

    /**
     * @throws ValidationException
     */
    public function assertExists(CrmSubjectType|string $type, int $subjectId): void
    {
        $subjectType = $type instanceof CrmSubjectType ? $type : CrmSubjectType::tryFrom($type);

        if (! $subjectType instanceof CrmSubjectType || $subjectId < 1 || ! $this->exists($subjectType, $subjectId)) {
            throw ValidationException::withMessages([
                'subject_id' => __('validation.exists', ['attribute' => __('crm.subject')]),
            ]);
        }
    }
}
