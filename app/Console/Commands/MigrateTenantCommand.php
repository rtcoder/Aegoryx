<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

final class MigrateTenantCommand extends Command
{
    protected $signature = 'tenant:migrate
        {tenant : Tenant ID or slug}
        {--force : Force migrations in production}';

    protected $description = 'Run tenant migrations for a single tenant.';

    public function handle(): int
    {
        $exitCode = Artisan::call('tenants:migrate', [
            '--tenant' => $this->argument('tenant'),
            '--force' => (bool) $this->option('force'),
        ]);

        $this->output->write(Artisan::output());

        return $exitCode;
    }
}
