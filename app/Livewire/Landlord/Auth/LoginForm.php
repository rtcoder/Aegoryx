<?php

namespace App\Livewire\Landlord\Auth;

use App\Models\Landlord\Identity;
use App\Modules\Identity\Enums\IdentityStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

        $identity = Identity::query()
            ->where('email', $credentials['email'])
            ->where('is_super_admin', true)
            ->where('status', IdentityStatus::Active->value)
            ->first();

        if (! $identity || ! Hash::check($credentials['password'], $identity->password)) {
            throw ValidationException::withMessages([
                'email' => 'Invalid landlord credentials.',
            ]);
        }

        if ($identity->hasTwoFactorEnabled()) {
            session(['landlord_login_2fa_identity_id' => $identity->id]);

            $this->redirectRoute('landlord.two-factor.challenge', navigate: true);

            return;
        }

        Auth::guard('landlord')->login($identity);
        request()->session()->regenerate();

        $this->redirectIntended(route('landlord.dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.landlord.auth.login-form');
    }
}
