<?php

namespace App\Modules\Crm\Http\Requests\Concerns;

use App\Modules\Crm\Enums\CrmSubjectType;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator;

trait ValidatesCrmSubject
{
    protected function prepareForValidation(): void
    {
        $subject = (string) $this->input('subject');

        if (! str_contains($subject, ':')) {
            return;
        }

        [$type, $id] = explode(':', $subject, 2);

        $this->merge([
            'subject_type' => $type,
            'subject_id' => $id,
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $type = CrmSubjectType::tryFrom((string) $this->input('subject_type'));
            $subjectId = $this->integer('subject_id');

            if (! $type instanceof CrmSubjectType || $subjectId < 1) {
                return;
            }

            $exists = DB::table($type->table())
                ->where('id', $subjectId)
                ->whereNull('deleted_at')
                ->exists();

            if (! $exists) {
                $validator->errors()->add('subject_id', __('validation.exists', ['attribute' => __('crm.subject')]));
            }
        });
    }
}
