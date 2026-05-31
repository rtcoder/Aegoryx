<?php

namespace App\Modules\Identity\Providers;

use App\Support\Modules\ModuleServiceProvider;

final class IdentityServiceProvider extends ModuleServiceProvider
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
