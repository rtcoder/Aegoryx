<?php

namespace App\Livewire\Admin\Auth;

use App\Models\System\Identity;
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
                'email' => 'Invalid system credentials.',
            ]);
        }

        if ($identity->hasTwoFactorEnabled()) {
            session(['admin_login_2fa_identity_id' => $identity->id]);

            $this->redirectRoute('admin.two-factor.challenge', navigate: true);

            return;
        }

        Auth::guard('admin')->login($identity);
        request()->session()->regenerate();

        $this->redirectIntended(route('admin.dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.auth.login-form');
    }
}
