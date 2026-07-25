<?php

namespace App\Modules\Identity\Actions;

use App\Models\Tenant\User;
use App\Modules\Identity\Notifications\PasswordResetLinkNotification;
use App\Modules\Identity\Support\PasswordResetTokens;

final readonly class RequestTenantPasswordResetAction
{
    public function __construct(
        private PasswordResetTokens $tokens,
    ) {}

    public function handle(string $email): ?string
    {
        $user = User::query()
            ->where('email', mb_strtolower(trim($email)))
            ->first();

        if (! $user) {
            return null;
        }

        $token = $this->tokens->create($user->email);

        $user->notify(new PasswordResetLinkNotification(route('tenant.password.reset', ['token' => $token])));

        return $token;
    }
}
