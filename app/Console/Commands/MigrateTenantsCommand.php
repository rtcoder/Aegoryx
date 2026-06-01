<?php

namespace App\Console\Commands;

use App\Models\Landlord\Tenant;
use App\Services\Tenancy\PostgresSchemaManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use RuntimeException;
use Throwable;

final class MigrateTenantsCommand extends Command
{
    protected $signature = 'tenants:migrate
        {--tenant= : Tenant ID or slug}
        {--force : Force migrations in production}
        {--continue-on-error : Continue migrating other tenants if one tenant fails}';

    protected $description = 'Run tenant migrations for all tenant schemas or one selected tenant.';

    public function handle(PostgresSchemaManager $schemas): int
    {
        $query = Tenant::query()->where('status', '!=', 'deleted');

        if ($tenant = $this->option('tenant')) {
            $query->where(function ($query) use ($tenant): void {
                $query->where('id', $tenant)->orWhere('slug', $tenant);
            });
        }

        $tenants = $query->orderBy('id')->get();

        if ($tenants->isEmpty()) {
            $this->warn('No tenants found.');

            return self::SUCCESS;
        }

        $failed = [];

        foreach ($tenants as $tenant) {
            $this->info("Migrating tenant [{$tenant->id}] [{$tenant->slug}] schema [{$tenant->schema_name}]...");

            try {
                $schemas->create($tenant->schema_name);
                $schemas->setSearchPath($tenant->schema_name);

                $exitCode = Artisan::call('migrate', [
                    '--path' => 'database/migrations/tenant',
                    '--force' => (bool) $this->option('force'),
                ]);

                $this->output->write(Artisan::output());

                if ($exitCode !== self::SUCCESS) {
                    throw new RuntimeException("Migration failed for tenant [{$tenant->id}] with exit code [{$exitCode}].");
                }
            } catch (Throwable $exception) {
                $failed[] = [
                    'tenant_id' => $tenant->id,
                    'schema' => $tenant->schema_name,
                    'error' => $exception->getMessage(),
                ];

                $this->error("Tenant [{$tenant->id}] migration failed: {$exception->getMessage()}");

                if (! $this->option('continue-on-error')) {
                    return self::FAILURE;
                }
            } finally {
                $schemas->resetSearchPath();
            }
        }

        if ($failed !== []) {
            $this->error('Some tenant migrations failed.');

            foreach ($failed as $failure) {
                $this->line("- Tenant {$failure['tenant_id']} / {$failure['schema']}: {$failure['error']}");
            }

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
