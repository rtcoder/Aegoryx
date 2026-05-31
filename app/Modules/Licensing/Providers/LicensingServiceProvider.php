<?php

namespace App\Modules\Licensing\Providers;

use App\Support\Modules\ModuleServiceProvider;

final class LicensingServiceProvider extends ModuleServiceProvider
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
