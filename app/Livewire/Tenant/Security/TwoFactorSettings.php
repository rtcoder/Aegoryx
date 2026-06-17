<?php

namespace App\Livewire\Tenant\Security;

use App\Models\Tenant\User;
use App\Modules\Identity\Actions\DisableTenantTwoFactorAuthAction;
use App\Modules\Identity\Actions\EnableTenantTwoFactorAuthAction;
use App\Modules\Identity\Support\TwoFactorAuthenticator;
use Illuminate\Contracts\View\View;
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
        $user = $this->user();

        $this->pendingSecret = $authenticator->generateSecret();
        $this->pendingRecoveryCodes = $authenticator->generateRecoveryCodes();
        $this->provisioningUri = $authenticator->provisioningUri(
            issuer: config('app.name', 'Aegoryx'),
            account: $user->email,
            secret: $this->pendingSecret,
        );
        $this->code = '';
    }

    public function enable(TwoFactorAuthenticator $authenticator, EnableTenantTwoFactorAuthAction $action): void
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

        $user = $this->user();

        $action->handle(
            user: $user,
            secret: $this->pendingSecret,
            recoveryCodes: $this->pendingRecoveryCodes,
            actor: $user,
            ip: request()->ip(),
            userAgent: request()->userAgent(),
        );

        $this->reset(['pendingSecret', 'pendingRecoveryCodes', 'provisioningUri', 'code']);

        session()->flash('success', __('two_factor.enabled_flash'));
    }

    public function disable(DisableTenantTwoFactorAuthAction $action): void
    {
        $user = $this->user();

        $action->handle(
            user: $user,
            actor: $user,
            ip: request()->ip(),
            userAgent: request()->userAgent(),
        );

        $this->reset(['pendingSecret', 'pendingRecoveryCodes', 'provisioningUri', 'code']);

        session()->flash('success', __('two_factor.disabled_flash'));
    }

    public function render(): View
    {
        return view('livewire.tenant.security.two-factor-settings', [
            'user' => $this->user()->refresh(),
        ]);
    }

    private function user(): User
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 403);

        return $user;
    }
}
