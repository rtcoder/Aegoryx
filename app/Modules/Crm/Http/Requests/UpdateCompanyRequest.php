<?php

namespace App\Modules\Crm\Http\Requests;

use App\Models\Tenant\CrmCompany;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        $company = $this->route('company');

        return $company instanceof CrmCompany
            && $this->user()?->can('update', $company) === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'email' => ['nullable', 'email:rfc', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'contact_ids' => ['array'],
            'contact_ids.*' => [
                'integer',
                Rule::exists('crm_contacts', 'id')->whereNull('deleted_at'),
            ],
        ];
    }
}
