<?php

namespace App\Modules\Crm\Providers;

use App\Support\Modules\ModuleServiceProvider;

final class CrmServiceProvider extends ModuleServiceProvider
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
