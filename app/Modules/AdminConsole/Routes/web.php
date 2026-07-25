<?php

use App\Http\Middleware\UseAuthenticatedLocale;
use App\Modules\AdminConsole\Http\Controllers\AuditLogController;
use App\Modules\AdminConsole\Http\Controllers\Auth\LoginController;
use App\Modules\AdminConsole\Http\Controllers\BillingEventController;
use App\Modules\AdminConsole\Http\Controllers\DashboardController;
use App\Modules\AdminConsole\Http\Controllers\LicenseController;
use App\Modules\AdminConsole\Http\Controllers\SectionController;
use App\Modules\AdminConsole\Http\Controllers\Security\SecurityController;
use App\Modules\AdminConsole\Http\Controllers\TenantController;
use App\Modules\AdminConsole\Http\Middleware\EnsureAdminAuthenticated;
use App\Modules\AdminConsole\Http\Middleware\UseSystemSchema;
use Illuminate\Support\Facades\Route;

Route::domain(config('aegoryx.admin.domain'))
    ->middleware(UseSystemSchema::class)
    ->name('admin.')
    ->group(function (): void {
        Route::get('/login', [LoginController::class, 'create'])->name('login');
        Route::post('/login', [LoginController::class, 'store'])->name('login.store');
        Route::get('/forgot-password', fn () => view('admin.auth.request-password-reset'))->name('password.request');
        Route::get('/reset-password/{token}', fn (string $token) => view('admin.auth.reset-password', ['token' => $token]))->name('password.reset');
        Route::get('/two-factor-challenge', fn () => view('admin.auth.two-factor-challenge'))->name('two-factor.challenge');

        Route::middleware([EnsureAdminAuthenticated::class, UseAuthenticatedLocale::class])->group(function (): void {
            Route::get('/', DashboardController::class)->name('dashboard');
            Route::get('/security', [SecurityController::class, 'index'])->name('security.index');
            Route::get('/tenants', [TenantController::class, 'index'])->name('tenants.index');
            Route::get('/tenants/{tenant}', [TenantController::class, 'show'])->name('tenants.show');
            Route::patch('/tenants/{tenant}/status', [TenantController::class, 'updateStatus'])->name('tenants.status.update');
            Route::get('/licenses', [LicenseController::class, 'index'])->name('licenses.index');
            Route::get('/licenses/{license}', [LicenseController::class, 'show'])->name('licenses.show');
            Route::post('/licenses/{license}/verify', [LicenseController::class, 'verify'])->name('licenses.verify');
            Route::get('/billing', [SectionController::class, 'billing'])->name('billing.index');
            Route::get('/billing/events/{event}', [BillingEventController::class, 'show'])->name('billing.events.show');
            Route::post('/billing/events/{event}/retry', [BillingEventController::class, 'retry'])->name('billing.events.retry');
            Route::get('/support', [SectionController::class, 'support'])->name('support.index');
            Route::get('/audit', [AuditLogController::class, 'index'])->name('audit.index');
            Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
        });
    });
