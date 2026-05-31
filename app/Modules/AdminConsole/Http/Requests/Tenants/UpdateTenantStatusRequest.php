<?php

namespace App\Modules\AdminConsole\Http\Requests\Tenants;

use App\Modules\Tenancy\Enums\TenantStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateTenantStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('landlord')?->is_super_admin === true;
    }

    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return [
            'status' => [
                'required',
                Rule::in([
                    TenantStatus::Active->value,
                    TenantStatus::Suspended->value,
                ]),
            ],
        ];
    }

    public function status(): TenantStatus
    {
        return TenantStatus::from($this->string('status')->toString());
    }
}
