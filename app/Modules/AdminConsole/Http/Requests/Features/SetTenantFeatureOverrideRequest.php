<?php

namespace App\Modules\AdminConsole\Http\Requests\Features;

use App\Models\Landlord\Identity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class SetTenantFeatureOverrideRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user('landlord');

        return $user instanceof Identity && $user->is_super_admin;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'tenant_id' => ['required', 'integer', Rule::exists('tenants', 'id')],
            'enabled' => ['required', 'boolean'],
            'reason' => ['required', 'string', 'max:1000'],
        ];
    }

    public function enabled(): bool
    {
        return $this->boolean('enabled');
    }
}
