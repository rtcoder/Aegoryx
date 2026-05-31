<?php

namespace App\Modules\Tenancy\Providers;

use App\Console\Commands\MigrateLandlordCommand;
use App\Console\Commands\MigrateTenantCommand;
use App\Console\Commands\MigrateTenantsCommand;
use App\Services\Tenancy\PostgresSchemaTenancyManager;
use App\Services\Tenancy\TenancyManager;
use App\Support\Modules\ModuleServiceProvider;

final class TenancyServiceProvider extends ModuleServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TenancyManager::class, PostgresSchemaTenancyManager::class);
    }

    public function boot(): void
    {
        $this->loadModuleRoutes();

        if ($this->app->runningInConsole()) {
            $this->commands([
                MigrateLandlordCommand::class,
                MigrateTenantsCommand::class,
                MigrateTenantCommand::class,
            ]);
        }
    }

    protected function moduleBasePath(): string
    {
        return dirname(__DIR__);
    }
}
