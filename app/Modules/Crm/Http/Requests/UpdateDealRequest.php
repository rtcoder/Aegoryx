<?php

namespace App\Modules\Crm\Http\Requests;

use App\Models\Tenant\CrmDeal;
use App\Modules\Crm\Enums\CrmDealStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateDealRequest extends FormRequest
{
    public function authorize(): bool
    {
        $deal = $this->route('deal');

        return $deal instanceof CrmDeal
            && $this->user()?->can('update', $deal) === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'company_id' => [
                'nullable',
                'integer',
                Rule::exists('crm_companies', 'id')->whereNull('deleted_at'),
            ],
            'contact_id' => [
                'nullable',
                'integer',
                Rule::exists('crm_contacts', 'id')->whereNull('deleted_at'),
            ],
            'status' => ['required', Rule::enum(CrmDealStatus::class)],
            'value_amount' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'currency' => ['nullable', 'string', 'size:3'],
            'expected_close_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
