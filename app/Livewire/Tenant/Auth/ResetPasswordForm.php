<?php

namespace App\Livewire\Tenant\Auth;

use App\Modules\Identity\Actions\ResetTenantPasswordAction;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class ResetPasswordForm extends Component
{
    public string $token;

    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|string|min:12|confirmed')]
    public string $password = '';

    public string $password_confirmation = '';

    public function mount(string $token): void
    {
        $this->token = $token;
    }

    public function resetPassword(ResetTenantPasswordAction $action): void
    {
        $this->validate();

        $action->handle($this->email, $this->token, $this->password);

        session()->flash('success', __('common.password_reset_completed'));

        $this->redirectRoute('tenant.login', navigate: true);
    }

    public function render()
    {
        return view('livewire.tenant.auth.reset-password-form');
    }
}
