<?php

namespace App\Modules\Identity\Actions;

use App\Models\System\Identity;
use App\Modules\Identity\Enums\IdentityStatus;
use App\Modules\Identity\Support\PasswordResetTokens;
use Illuminate\Validation\ValidationException;

final readonly class ResetSuperadminPasswordAction
{
    public function __construct(
        private PasswordResetTokens $tokens,
    ) {}

    public function handle(string $email, string $token, string $password): Identity
    {
        $email = mb_strtolower(trim($email));
        $identity = Identity::query()
            ->where('email', $email)
            ->where('is_super_admin', true)
            ->where('status', IdentityStatus::Active->value)
            ->first();

        if (! $identity || ! $this->tokens->isValid($email, $token)) {
            throw ValidationException::withMessages([
                'email' => __('common.password_reset_invalid'),
            ]);
        }

        $identity->forceFill(['password' => $password])->save();
        $this->tokens->delete($email);

        return $identity->refresh();
    }
}
