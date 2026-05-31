<?php

namespace App\Console\Commands;

use App\Services\Tenancy\PostgresSchemaManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;

final class RollbackLandlordMigrationCommand extends Command
{
    protected $signature = 'landlord:migrate:rollback
        {--migration=* : Migration name to roll back}
        {--force : Force rollback in production}';

    protected $description = 'Roll back specific landlord migrations from the public schema.';

    public function handle(PostgresSchemaManager $schemas): int
    {
        $migrations = $this->option('migration');

        if ($migrations === []) {
            $this->warn('No landlord migrations were provided for rollback.');

            return self::SUCCESS;
        }

        $schemas->usePublicSchema();

        try {
            foreach ($migrations as $migration) {
                $this->rollbackMigration($migration);
            }

            return self::SUCCESS;
        } finally {
            $schemas->resetSearchPath();
        }
    }

    private function rollbackMigration(string $migration): void
    {
        $migrationFile = database_path("migrations/landlord/{$migration}.php");

        if (! is_file($migrationFile)) {
            throw new RuntimeException("Landlord migration file [{$migrationFile}] does not exist.");
        }

        $this->info("Rolling back landlord migration [{$migration}]...");

        $migrationInstance = require $migrationFile;
        $migrationInstance->down();

        DB::table('migrations')
            ->where('migration', $migration)
            ->delete();
    }
}
