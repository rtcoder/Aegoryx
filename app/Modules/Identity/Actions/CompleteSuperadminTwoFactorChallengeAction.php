<?php

namespace App\Modules\Identity\Actions;

use App\Models\System\Identity;
use App\Modules\Identity\Support\TwoFactorAuthenticator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final readonly class CompleteSuperadminTwoFactorChallengeAction
{
    public function __construct(
        private TwoFactorAuthenticator $authenticator,
    ) {}

    public function handle(Identity $identity, string $code): void
    {
        if (! $identity->hasTwoFactorEnabled()) {
            throw ValidationException::withMessages([
                'code' => __('two_factor.challenge_not_available'),
            ]);
        }

        if ($this->authenticator->verifyCode($identity->two_factor_secret, $code)) {
            Auth::guard('admin')->login($identity);

            return;
        }

        $recoveryCodes = $identity->two_factor_recovery_codes ?? [];

        foreach ($recoveryCodes as $index => $hashedCode) {
            if (! Hash::check($code, $hashedCode)) {
                continue;
            }

            unset($recoveryCodes[$index]);
            $identity->forceFill([
                'two_factor_recovery_codes' => array_values($recoveryCodes),
            ])->save();

            Auth::guard('admin')->login($identity->refresh());

            return;
        }

        throw ValidationException::withMessages([
            'code' => __('two_factor.invalid_code'),
        ]);
    }
}
