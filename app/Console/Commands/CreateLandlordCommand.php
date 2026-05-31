<?php

namespace App\Console\Commands;

use App\Models\Landlord\Identity;
use App\Services\Tenancy\PostgresSchemaManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

final class CreateLandlordCommand extends Command
{
    protected $signature = 'landlord:create
        {email : Superadmin email address}
        {--password= : Optional password. If omitted, a strong password is generated.}';

    protected $description = 'Create or update a landlord superadmin identity.';

    public function handle(PostgresSchemaManager $schemas): int
    {
        $email = Str::lower((string) $this->argument('email'));
        $password = $this->option('password');
        $generatedPassword = null;

        $validator = Validator::make(
            [
                'email' => $email,
                'password' => $password,
            ],
            [
                'email' => ['required', 'email:rfc'],
                'password' => ['nullable', Password::min(16)->letters()->mixedCase()->numbers()->symbols()],
            ],
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        if (! is_string($password) || $password === '') {
            $generatedPassword = Str::password(length: 32, letters: true, numbers: true, symbols: true, spaces: false);
            $password = $generatedPassword;
        }

        $schemas->usePublicSchema();

        try {
            $identity = Identity::query()->withTrashed()->firstOrNew(['email' => $email]);

            $identity->fill([
                'email' => $email,
                'password' => $password,
                'status' => 'active',
                'is_super_admin' => true,
            ]);

            if ($identity->trashed()) {
                $identity->restore();
            }

            $identity->save();

            $this->info("Superadmin [{$email}] is ready.");

            if ($generatedPassword !== null) {
                $this->warn('Generated password. Store it now; it will not be shown again.');
                $this->line($generatedPassword);
            }

            return self::SUCCESS;
        } finally {
            $schemas->resetSearchPath();
        }
    }
}
