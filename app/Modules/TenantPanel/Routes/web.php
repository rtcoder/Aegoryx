<?php

use App\Modules\TenantPanel\Http\Controllers\DashboardController;
use App\Modules\TenantPanel\Http\Middleware\ResolveTenantFromDomain;
use Illuminate\Support\Facades\Route;

Route::middleware(ResolveTenantFromDomain::class)
    ->name('tenant.')
    ->group(function (): void {
        Route::get('/panel', DashboardController::class)->name('dashboard');
    });
