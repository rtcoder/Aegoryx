<?php

namespace App\Modules\Auth\Providers;

use App\Support\Modules\ModuleServiceProvider;

final class AuthServiceProvider extends ModuleServiceProvider
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
