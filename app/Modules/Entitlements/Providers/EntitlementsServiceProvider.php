<?php

namespace App\Modules\Entitlements\Providers;

use App\Support\Modules\ModuleServiceProvider;

final class EntitlementsServiceProvider extends ModuleServiceProvider
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
