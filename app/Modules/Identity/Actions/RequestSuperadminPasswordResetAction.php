<?php

namespace App\Modules\Identity\Actions;

use App\Models\System\Identity;
use App\Modules\Identity\Enums\IdentityStatus;
use App\Modules\Identity\Notifications\PasswordResetLinkNotification;
use App\Modules\Identity\Support\PasswordResetTokens;

final readonly class RequestSuperadminPasswordResetAction
{
    public function __construct(
        private PasswordResetTokens $tokens,
    ) {}

    public function handle(string $email): ?string
    {
        $identity = Identity::query()
            ->where('email', mb_strtolower(trim($email)))
            ->where('is_super_admin', true)
            ->where('status', IdentityStatus::Active->value)
            ->first();

        if (! $identity) {
            return null;
        }

        $token = $this->tokens->create($identity->email);

        $identity->notify(new PasswordResetLinkNotification(route('admin.password.reset', ['token' => $token])));

        return $token;
    }
}
