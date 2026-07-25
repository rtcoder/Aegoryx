<?php

namespace App\Livewire\Admin\Auth;

use App\Models\System\Identity;
use App\Modules\Identity\Actions\CompleteSuperadminTwoFactorChallengeAction;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class TwoFactorChallengeForm extends Component
{
    #[Validate('required|string')]
    public string $code = '';

    public string $email = '';

    public function mount(): void
    {
        $identityId = session('admin_login_2fa_identity_id');

        if (! $identityId) {
            $this->redirectRoute('admin.login', navigate: true);

            return;
        }

        $identity = Identity::query()->find($identityId);

        if (! $identity) {
            session()->forget('admin_login_2fa_identity_id');
            $this->redirectRoute('admin.login', navigate: true);

            return;
        }

        $this->email = $identity->email;
    }

    public function verify(CompleteSuperadminTwoFactorChallengeAction $action): void
    {
        $this->validate();

        $identity = Identity::query()->findOrFail(session('admin_login_2fa_identity_id'));

        $action->handle($identity, $this->code);

        session()->forget('admin_login_2fa_identity_id');

        if (request()->hasSession()) {
            request()->session()->regenerate();
        }

        $this->redirectIntended(route('admin.dashboard'), navigate: true);
    }

    public function cancel(): void
    {
        session()->forget('admin_login_2fa_identity_id');

        $this->redirectRoute('admin.login', navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.auth.two-factor-challenge-form');
    }
}
