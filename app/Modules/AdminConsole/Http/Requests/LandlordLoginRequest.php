<?php

namespace App\Modules\AdminConsole\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class LandlordLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email:rfc'],
            'password' => ['required', 'string'],
        ];
    }
}
