<?php

namespace App\Providers;

use App\Console\Commands\PreflightCommand;
use App\Console\Commands\PurgeRetentionCommand;
use App\Console\Commands\SmokeCommand;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                PreflightCommand::class,
                PurgeRetentionCommand::class,
                SmokeCommand::class,
            ]);
        }
    }
}
