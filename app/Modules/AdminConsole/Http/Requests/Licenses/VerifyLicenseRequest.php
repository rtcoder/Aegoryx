<?php

namespace App\Modules\AdminConsole\Http\Requests\Licenses;

use App\Models\System\Identity;
use Illuminate\Foundation\Http\FormRequest;

final class VerifyLicenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user('admin');

        return $user instanceof Identity && $user->is_super_admin;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }
}
