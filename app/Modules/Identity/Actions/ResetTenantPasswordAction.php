<?php

namespace App\Modules\Identity\Actions;

use App\Models\Tenant\User;
use App\Modules\Identity\Support\PasswordResetTokens;
use Illuminate\Validation\ValidationException;

final readonly class ResetTenantPasswordAction
{
    public function __construct(
        private PasswordResetTokens $tokens,
    ) {}

    public function handle(string $email, string $token, string $password): User
    {
        $email = mb_strtolower(trim($email));
        $user = User::query()->where('email', $email)->first();

        if (! $user || ! $this->tokens->isValid($email, $token)) {
            throw ValidationException::withMessages([
                'email' => __('common.password_reset_invalid'),
            ]);
        }

        $user->forceFill(['password' => $password])->save();
        $this->tokens->delete($email);

        return $user->refresh();
    }
}
