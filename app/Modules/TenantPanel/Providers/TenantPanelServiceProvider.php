<?php

namespace App\Modules\TenantPanel\Providers;

use App\Support\Modules\ModuleServiceProvider;

final class TenantPanelServiceProvider extends ModuleServiceProvider
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
