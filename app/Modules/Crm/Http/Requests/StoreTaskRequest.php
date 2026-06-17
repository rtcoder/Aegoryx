<?php

namespace App\Modules\Crm\Http\Requests;

use App\Models\Tenant\CrmTask;
use App\Modules\Crm\Enums\CrmSubjectType;
use App\Modules\Crm\Enums\CrmTaskStatus;
use App\Modules\Crm\Http\Requests\Concerns\ValidatesCrmSubject;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreTaskRequest extends FormRequest
{
    use ValidatesCrmSubject;

    public function authorize(): bool
    {
        return $this->user()?->can('create', CrmTask::class) === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'subject' => ['required', 'string'],
            'subject_type' => ['required', Rule::enum(CrmSubjectType::class)],
            'subject_id' => ['required', 'integer', 'min:1'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'status' => ['required', Rule::enum(CrmTaskStatus::class)],
            'due_date' => ['nullable', 'date'],
            'assigned_to' => ['nullable', 'integer', Rule::exists('users', 'id')->whereNull('deleted_at')],
        ];
    }
}
