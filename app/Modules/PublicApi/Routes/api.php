<?php

use App\Modules\PublicApi\Http\Controllers\PublishedPageController;
use App\Modules\PublicApi\Http\Middleware\EnsurePublicApiCors;
use App\Modules\PublicApi\Http\Middleware\ResolvePublicTenantFromDomain;
use App\Modules\PublicApi\Http\Middleware\ThrottlePublicApi;
use Illuminate\Support\Facades\Route;

Route::middleware([
    ResolvePublicTenantFromDomain::class,
    EnsurePublicApiCors::class,
    ThrottlePublicApi::class,
])
    ->prefix('public')
    ->name('public-api.')
    ->group(function (): void {
        Route::options('{any}', fn () => response()->noContent())
            ->where('any', '.*')
            ->name('options');
        Route::prefix('v1')
            ->name('v1.')
            ->group(function (): void {
                Route::get('cms/pages/{slug}', [PublishedPageController::class, 'show'])
                    ->name('cms.pages.show');
            });
        Route::get('cms/pages/{slug}', [PublishedPageController::class, 'show'])
            ->name('cms.pages.show');
    });
