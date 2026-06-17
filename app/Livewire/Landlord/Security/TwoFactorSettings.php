<?php

namespace App\Livewire\Landlord\Security;

use App\Modules\Identity\Actions\DisableTwoFactorAuthAction;
use App\Modules\Identity\Actions\EnableTwoFactorAuthAction;
use App\Modules\Identity\Support\TwoFactorAuthenticator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class TwoFactorSettings extends Component
{
    public ?string $pendingSecret = null;

    /** @var list<string> */
    public array $pendingRecoveryCodes = [];

    public ?string $provisioningUri = null;

    #[Validate('required|string')]
    public string $code = '';

    public function generate(TwoFactorAuthenticator $authenticator): void
    {
        $identity = Auth::guard('landlord')->user();

        $this->pendingSecret = $authenticator->generateSecret();
        $this->pendingRecoveryCodes = $authenticator->generateRecoveryCodes();
        $this->provisioningUri = $authenticator->provisioningUri(
            issuer: config('app.name', 'Aegoryx'),
            account: $identity->email,
            secret: $this->pendingSecret,
        );
        $this->code = '';
    }

    public function enable(TwoFactorAuthenticator $authenticator, EnableTwoFactorAuthAction $action): void
    {
        $this->validate();

        if ($this->pendingSecret === null || $this->pendingRecoveryCodes === []) {
            $this->addError('code', __('two_factor.setup_required'));

            return;
        }

        if (! $authenticator->verifyCode($this->pendingSecret, $this->code)) {
            $this->addError('code', __('two_factor.invalid_code'));

            return;
        }

        $identity = Auth::guard('landlord')->user();

        $action->handle(
            identity: $identity,
            secret: $this->pendingSecret,
            recoveryCodes: $this->pendingRecoveryCodes,
            actor: $identity,
            ip: request()->ip(),
            userAgent: request()->userAgent(),
        );

        $this->reset(['pendingSecret', 'pendingRecoveryCodes', 'provisioningUri', 'code']);

        session()->flash('success', __('two_factor.enabled_flash'));
    }

    public function disable(DisableTwoFactorAuthAction $action): void
    {
        $identity = Auth::guard('landlord')->user();

        $action->handle(
            identity: $identity,
            actor: $identity,
            ip: request()->ip(),
            userAgent: request()->userAgent(),
        );

        $this->reset(['pendingSecret', 'pendingRecoveryCodes', 'provisioningUri', 'code']);

        session()->flash('success', __('two_factor.disabled_flash'));
    }

    public function render()
    {
        return view('livewire.landlord.security.two-factor-settings', [
            'identity' => Auth::guard('landlord')->user()->refresh(),
        ]);
    }
}
