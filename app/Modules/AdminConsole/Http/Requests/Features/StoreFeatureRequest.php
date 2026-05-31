<?php

namespace App\Modules\AdminConsole\Http\Requests\Features;

use App\Models\Landlord\Identity;
use App\Modules\Entitlements\Enums\FeatureStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreFeatureRequest extends FormRequest
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
            'key' => ['required', 'string', 'max:120', 'regex:/^[A-Za-z0-9_.-]+$/', 'unique:features,key'],
            'name' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', Rule::enum(FeatureStatus::class)],
        ];
    }

    public function status(): FeatureStatus
    {
        return FeatureStatus::from($this->string('status')->toString());
    }
}
