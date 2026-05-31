<?php

namespace App\Modules\Files\Providers;

use App\Support\Modules\ModuleServiceProvider;

final class FilesServiceProvider extends ModuleServiceProvider
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
