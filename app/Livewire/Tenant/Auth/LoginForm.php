<?php

namespace App\Livewire\Tenant\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class LoginForm extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public function login(): void
    {
        $this->validate();

        if (! Auth::guard('web')->attempt([
            'email' => mb_strtolower($this->email),
            'password' => $this->password,
        ])) {
            throw ValidationException::withMessages([
                'email' => __('errors.invalid_tenant_credentials'),
            ]);
        }

        request()->session()->regenerate();

        $this->redirectIntended(route('tenant.dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.tenant.auth.login-form');
    }
}
