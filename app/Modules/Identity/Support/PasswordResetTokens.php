<?php

namespace App\Modules\Identity\Support;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final readonly class PasswordResetTokens
{
    public function create(string $email): string
    {
        $email = $this->normalizeEmail($email);
        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ],
        );

        return $token;
    }

    public function isValid(string $email, string $token): bool
    {
        $record = DB::table('password_reset_tokens')
            ->where('email', $this->normalizeEmail($email))
            ->first();

        if (! $record || ! is_string($record->token)) {
            return false;
        }

        if ($this->isExpired($record->created_at)) {
            return false;
        }

        return Hash::check($token, $record->token);
    }

    public function delete(string $email): void
    {
        DB::table('password_reset_tokens')
            ->where('email', $this->normalizeEmail($email))
            ->delete();
    }

    private function isExpired(mixed $createdAt): bool
    {
        if ($createdAt === null) {
            return true;
        }

        return Carbon::parse($createdAt)
            ->addMinutes((int) config('auth.passwords.users.expire', 60))
            ->isPast();
    }

    private function normalizeEmail(string $email): string
    {
        return mb_strtolower(trim($email));
    }
}
