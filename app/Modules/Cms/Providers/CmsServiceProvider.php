<?php

namespace App\Modules\Cms\Providers;

use App\Support\Modules\ModuleServiceProvider;

final class CmsServiceProvider extends ModuleServiceProvider
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
