<?php

use App\Modules\AdminConsole\Http\Controllers\Auth\LoginController;
use App\Modules\AdminConsole\Http\Controllers\DashboardController;
use App\Modules\AdminConsole\Http\Controllers\FeatureController;
use App\Modules\AdminConsole\Http\Controllers\LicenseController;
use App\Modules\AdminConsole\Http\Controllers\SectionController;
use App\Modules\AdminConsole\Http\Controllers\TenantController;
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
            Route::get('/tenants', [TenantController::class, 'index'])->name('tenants.index');
            Route::get('/tenants/{tenant}', [TenantController::class, 'show'])->name('tenants.show');
            Route::patch('/tenants/{tenant}/status', [TenantController::class, 'updateStatus'])->name('tenants.status.update');
            Route::get('/features', [FeatureController::class, 'index'])->name('features.index');
            Route::post('/features', [FeatureController::class, 'store'])->name('features.store');
            Route::get('/features/{feature}', [FeatureController::class, 'show'])->name('features.show');
            Route::patch('/features/{feature}/status', [FeatureController::class, 'updateStatus'])->name('features.status.update');
            Route::post('/features/{feature}/tenant-overrides', [FeatureController::class, 'setTenantOverride'])->name('features.tenant-overrides.store');
            Route::get('/licenses', [LicenseController::class, 'index'])->name('licenses.index');
            Route::get('/licenses/{license}', [LicenseController::class, 'show'])->name('licenses.show');
            Route::post('/licenses/{license}/verify', [LicenseController::class, 'verify'])->name('licenses.verify');
            Route::get('/billing', [SectionController::class, 'billing'])->name('billing.index');
            Route::get('/support', [SectionController::class, 'support'])->name('support.index');
            Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
        });
    });
