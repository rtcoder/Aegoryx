<?php

namespace App\Modules\AdminConsole\Providers;

use App\Support\Modules\ModuleServiceProvider;

final class AdminConsoleServiceProvider extends ModuleServiceProvider
{
    public function boot(): void
    {
        $this->loadModuleRoutes();
    }

    protected function moduleBasePath(): string
    {
        return dirname(__DIR__);
    }
}
