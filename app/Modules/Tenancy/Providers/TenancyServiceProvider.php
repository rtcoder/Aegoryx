<?php

namespace App\Modules\Tenancy\Providers;

use App\Console\Commands\CreateTenantCommand;
use App\Console\Commands\MigrateLandlordCommand;
use App\Console\Commands\MigrateTenantCommand;
use App\Console\Commands\MigrateTenantsCommand;
use App\Console\Commands\RollbackLandlordMigrationCommand;
use App\Console\Commands\RollbackTenantsMigrationCommand;
use App\Console\Commands\VerifyTenantDomainsCommand;
use App\Modules\Tenancy\Services\DnsTxtResolver;
use App\Modules\Tenancy\Services\NativeDnsTxtResolver;
use App\Services\Tenancy\PostgresSchemaTenancyManager;
use App\Services\Tenancy\TenancyManager;
use App\Support\Modules\ModuleServiceProvider;

final class TenancyServiceProvider extends ModuleServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TenancyManager::class, PostgresSchemaTenancyManager::class);
        $this->app->singleton(DnsTxtResolver::class, NativeDnsTxtResolver::class);
    }

    public function boot(): void
    {
        $this->loadModuleRoutes();

        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateTenantCommand::class,
                MigrateLandlordCommand::class,
                MigrateTenantsCommand::class,
                MigrateTenantCommand::class,
                RollbackLandlordMigrationCommand::class,
                RollbackTenantsMigrationCommand::class,
                VerifyTenantDomainsCommand::class,
            ]);
        }
    }

    protected function moduleBasePath(): string
    {
        return dirname(__DIR__);
    }
}
