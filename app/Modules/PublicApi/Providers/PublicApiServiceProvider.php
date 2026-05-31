<?php

namespace App\Modules\PublicApi\Providers;

use App\Support\Modules\ModuleServiceProvider;

final class PublicApiServiceProvider extends ModuleServiceProvider
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
