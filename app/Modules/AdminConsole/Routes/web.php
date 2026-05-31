<?php

use App\Modules\AdminConsole\Http\Controllers\Auth\LoginController;
use App\Modules\AdminConsole\Http\Controllers\DashboardController;
use App\Modules\AdminConsole\Http\Controllers\SectionController;
use App\Modules\AdminConsole\Http\Middleware\EnsureLandlordAuthenticated;
use App\Modules\AdminConsole\Http\Middleware\UseLandlordSchema;
use Illuminate\Support\Facades\Route;

Route::domain(config('aegoryx.landlord.domain'))
    ->middleware(UseLandlordSchema::class)
    ->name('landlord.')
    ->group(function (): void {
        Route::get('/login', [LoginController::class, 'create'])->name('login');
        Route::post('/login', [LoginController::class, 'store'])->name('login.store');

        Route::middleware(EnsureLandlordAuthenticated::class)->group(function (): void {
            Route::get('/', DashboardController::class)->name('dashboard');
            Route::get('/tenants', [SectionController::class, 'tenants'])->name('tenants.index');
            Route::get('/features', [SectionController::class, 'features'])->name('features.index');
            Route::get('/licenses', [SectionController::class, 'licenses'])->name('licenses.index');
            Route::get('/billing', [SectionController::class, 'billing'])->name('billing.index');
            Route::get('/support', [SectionController::class, 'support'])->name('support.index');
            Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
        });
    });
