<?php

namespace App\Support\Modules;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

abstract class ModuleServiceProvider extends ServiceProvider
{
    abstract protected function moduleBasePath(): string;

    protected function loadModuleRoutes(): void
    {
        $routesPath = $this->moduleBasePath().'/Routes';

        if (is_file($routesPath.'/web.php')) {
            Route::middleware('web')->group($routesPath.'/web.php');
        }

        if (is_file($routesPath.'/api.php')) {
            Route::prefix('api')->middleware('api')->group($routesPath.'/api.php');
        }
    }
}
