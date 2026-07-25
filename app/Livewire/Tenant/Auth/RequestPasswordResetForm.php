<?php

namespace App\Livewire\Tenant\Auth;

use App\Modules\Identity\Actions\RequestTenantPasswordResetAction;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class RequestPasswordResetForm extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    public ?string $token = null;

    public bool $showDevToken = false;

    public function requestReset(RequestTenantPasswordResetAction $action): void
    {
        $this->validate();

        $this->token = $action->handle($this->email);
        $this->showDevToken = (bool) ($this->token && (app()->isLocal() || app()->runningUnitTests()));

        session()->flash('success', __('common.password_reset_requested'));
    }

    public function render()
    {
        return view('livewire.tenant.auth.request-password-reset-form');
    }
}
