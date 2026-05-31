<?php

namespace App\Console\Commands;

use App\Models\Landlord\Tenant;
use App\Services\Tenancy\PostgresSchemaManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;

final class RollbackTenantsMigrationCommand extends Command
{
    protected $signature = 'tenants:migrate:rollback
        {--tenant= : Tenant ID or slug}
        {--schema= : Tenant schema name}
        {--migration=* : Migration name to roll back}
        {--force : Force rollback in production}';

    protected $description = 'Roll back specific tenant migrations from a tenant schema.';

    public function handle(PostgresSchemaManager $schemas): int
    {
        $migrations = $this->option('migration');
        $schema = $this->schema();

        if ($migrations === []) {
            $this->warn('No tenant migrations were provided for rollback.');

            return self::SUCCESS;
        }

        $schemas->setSearchPath($schema);

        try {
            foreach ($migrations as $migration) {
                $this->rollbackMigration($migration, $schema);
            }

            return self::SUCCESS;
        } finally {
            $schemas->resetSearchPath();
        }
    }

    private function schema(): string
    {
        if ($schema = $this->option('schema')) {
            return $schema;
        }

        if (! $tenant = $this->option('tenant')) {
            throw new RuntimeException('Provide --schema or --tenant for tenant migration rollback.');
        }

        return Tenant::query()
            ->where('id', $tenant)
            ->orWhere('slug', $tenant)
            ->value('schema_name')
            ?? throw new RuntimeException("Tenant [{$tenant}] was not found.");
    }

    private function rollbackMigration(string $migration, string $schema): void
    {
        $migrationFile = database_path("migrations/tenant/{$migration}.php");

        if (! is_file($migrationFile)) {
            throw new RuntimeException("Tenant migration file [{$migrationFile}] does not exist.");
        }

        $this->info("Rolling back tenant schema [{$schema}] migration [{$migration}]...");

        $migrationInstance = require $migrationFile;
        $migrationInstance->down();

        DB::table('migrations')
            ->where('migration', $migration)
            ->delete();
    }
}
