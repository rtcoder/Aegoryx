<?php

namespace App\Console\Commands;

use App\Services\Tenancy\PostgresSchemaManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

final class MigrateLandlordCommand extends Command
{
    protected $signature = 'landlord:migrate {--force : Force migrations in production}';

    protected $description = 'Run landlord migrations on the public schema.';

    public function handle(PostgresSchemaManager $schemas): int
    {
        $schemas->usePublicSchema();

        try {
            $exitCode = Artisan::call('migrate', [
                '--path' => 'database/migrations/landlord',
                '--database' => 'pgsql',
                '--force' => (bool) $this->option('force'),
            ]);

            $this->output->write(Artisan::output());

            return $exitCode;
        } finally {
            $schemas->resetSearchPath();
        }
    }
}
