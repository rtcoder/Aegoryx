<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

final class AegoryxModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        foreach (config('aegoryx.modules', []) as $module) {
            if (! ($module['enabled'] ?? false)) {
                continue;
            }

            $provider = $module['provider'] ?? null;

            if (is_string($provider) && class_exists($provider)) {
                $this->app->register($provider);
            }
        }
    }
}
