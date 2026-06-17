<?php

namespace App\Modules\Crm\Http\Requests;

use App\Modules\Crm\Enums\CrmSubjectType;
use App\Modules\Crm\Http\Requests\Concerns\ValidatesCrmSubject;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateNoteRequest extends FormRequest
{
    use ValidatesCrmSubject;

    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('note')) === true;
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
            'body' => ['required', 'string', 'max:10000'],
            'is_sensitive' => ['sometimes', 'boolean'],
        ];
    }
}
