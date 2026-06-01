<?php

use App\Modules\PublicApi\Http\Controllers\PublishedPageController;
use App\Modules\PublicApi\Http\Middleware\ResolvePublicTenantFromDomain;
use Illuminate\Support\Facades\Route;

Route::middleware(ResolvePublicTenantFromDomain::class)
    ->prefix('public')
    ->name('public-api.')
    ->group(function (): void {
        Route::get('cms/pages/{slug}', [PublishedPageController::class, 'show'])
            ->name('cms.pages.show');
    });
