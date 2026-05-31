<?php

use App\Modules\AdminConsole\Http\Controllers\Auth\LoginController;
use App\Modules\AdminConsole\Http\Controllers\DashboardController;
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
            Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
        });
    });
