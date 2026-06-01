<?php

namespace App\Livewire\Landlord\Auth;

use App\Modules\Identity\Enums\IdentityStatus;
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

        $credentials = [
            'email' => mb_strtolower($this->email),
            'password' => $this->password,
            'is_super_admin' => true,
            'status' => IdentityStatus::Active->value,
        ];

        if (! Auth::guard('landlord')->attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => 'Invalid landlord credentials.',
            ]);
        }

        request()->session()->regenerate();

        $this->redirectIntended(route('landlord.dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.landlord.auth.login-form');
    }
}
