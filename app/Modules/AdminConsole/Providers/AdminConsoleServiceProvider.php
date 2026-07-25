<?php

namespace App\Modules\AdminConsole\Providers;

use App\Console\Commands\CreateSuperadminCommand;
use App\Support\Modules\ModuleServiceProvider;

final class AdminConsoleServiceProvider extends ModuleServiceProvider
{
    public function boot(): void
    {
        $this->loadModuleRoutes();

        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateSuperadminCommand::class,
            ]);
        }
    }

    protected function moduleBasePath(): string
    {
        return dirname(__DIR__);
    }
}
