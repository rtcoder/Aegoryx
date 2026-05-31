<?php

namespace App\Modules\AdminConsole\Providers;

use App\Console\Commands\CreateLandlordCommand;
use App\Support\Modules\ModuleServiceProvider;

final class AdminConsoleServiceProvider extends ModuleServiceProvider
{
    public function boot(): void
    {
        $this->loadModuleRoutes();

        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateLandlordCommand::class,
            ]);
        }
    }

    protected function moduleBasePath(): string
    {
        return dirname(__DIR__);
    }
}
