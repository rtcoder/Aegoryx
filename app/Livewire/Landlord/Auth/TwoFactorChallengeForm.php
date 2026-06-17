<?php

namespace App\Livewire\Landlord\Auth;

use App\Models\Landlord\Identity;
use App\Modules\Identity\Actions\CompleteLandlordTwoFactorChallengeAction;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class TwoFactorChallengeForm extends Component
{
    #[Validate('required|string')]
    public string $code = '';

    public string $email = '';

    public function mount(): void
    {
        $identityId = session('landlord_login_2fa_identity_id');

        if (! $identityId) {
            $this->redirectRoute('landlord.login', navigate: true);

            return;
        }

        $identity = Identity::query()->find($identityId);

        if (! $identity) {
            session()->forget('landlord_login_2fa_identity_id');
            $this->redirectRoute('landlord.login', navigate: true);

            return;
        }

        $this->email = $identity->email;
    }

    public function verify(CompleteLandlordTwoFactorChallengeAction $action): void
    {
        $this->validate();

        $identity = Identity::query()->findOrFail(session('landlord_login_2fa_identity_id'));

        $action->handle($identity, $this->code);

        session()->forget('landlord_login_2fa_identity_id');

        if (request()->hasSession()) {
            request()->session()->regenerate();
        }

        $this->redirectIntended(route('landlord.dashboard'), navigate: true);
    }

    public function cancel(): void
    {
        session()->forget('landlord_login_2fa_identity_id');

        $this->redirectRoute('landlord.login', navigate: true);
    }

    public function render()
    {
        return view('livewire.landlord.auth.two-factor-challenge-form');
    }
}
