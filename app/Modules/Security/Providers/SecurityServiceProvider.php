<?php

namespace App\Modules\Security\Providers;

use App\Support\Modules\ModuleServiceProvider;

final class SecurityServiceProvider extends ModuleServiceProvider
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
