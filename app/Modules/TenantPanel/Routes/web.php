<?php

use App\Modules\Crm\Http\Controllers\CompanyController;
use App\Modules\Crm\Http\Controllers\ContactController;
use App\Modules\Crm\Http\Controllers\DealController;
use App\Modules\TenantPanel\Http\Controllers\Auth\LoginController;
use App\Modules\TenantPanel\Http\Controllers\DashboardController;
use App\Modules\TenantPanel\Http\Controllers\Modules\ModulePageController;
use App\Modules\TenantPanel\Http\Middleware\EnsureTenantAuthenticated;
use App\Modules\TenantPanel\Http\Middleware\EnsureTenantFeatureEnabled;
use App\Modules\TenantPanel\Http\Middleware\ResolveTenantFromDomain;
use Illuminate\Support\Facades\Route;

Route::middleware(ResolveTenantFromDomain::class)
    ->name('tenant.')
    ->group(function (): void {
        Route::get('/login', [LoginController::class, 'create'])->name('login');

        Route::middleware(EnsureTenantAuthenticated::class)->group(function (): void {
            Route::get('/panel', DashboardController::class)->name('dashboard');
            Route::get('/panel/cms', [ModulePageController::class, 'cms'])
                ->middleware(EnsureTenantFeatureEnabled::class.':cms')
                ->name('cms.index');
            Route::get('/panel/crm', [ContactController::class, 'index'])
                ->middleware(EnsureTenantFeatureEnabled::class.':crm')
                ->name('crm.index');
            Route::get('/panel/crm/companies', [CompanyController::class, 'index'])
                ->middleware(EnsureTenantFeatureEnabled::class.':crm')
                ->name('crm.companies.index');
            Route::post('/panel/crm/companies', [CompanyController::class, 'store'])
                ->middleware(EnsureTenantFeatureEnabled::class.':crm')
                ->name('crm.companies.store');
            Route::get('/panel/crm/companies/{company}/edit', [CompanyController::class, 'edit'])
                ->middleware(EnsureTenantFeatureEnabled::class.':crm')
                ->name('crm.companies.edit');
            Route::patch('/panel/crm/companies/{company}', [CompanyController::class, 'update'])
                ->middleware(EnsureTenantFeatureEnabled::class.':crm')
                ->name('crm.companies.update');
            Route::delete('/panel/crm/companies/{company}', [CompanyController::class, 'destroy'])
                ->middleware(EnsureTenantFeatureEnabled::class.':crm')
                ->name('crm.companies.destroy');
            Route::get('/panel/crm/deals', [DealController::class, 'index'])
                ->middleware(EnsureTenantFeatureEnabled::class.':crm')
                ->name('crm.deals.index');
            Route::post('/panel/crm/deals', [DealController::class, 'store'])
                ->middleware(EnsureTenantFeatureEnabled::class.':crm')
                ->name('crm.deals.store');
            Route::get('/panel/crm/deals/{deal}/edit', [DealController::class, 'edit'])
                ->middleware(EnsureTenantFeatureEnabled::class.':crm')
                ->name('crm.deals.edit');
            Route::patch('/panel/crm/deals/{deal}', [DealController::class, 'update'])
                ->middleware(EnsureTenantFeatureEnabled::class.':crm')
                ->name('crm.deals.update');
            Route::delete('/panel/crm/deals/{deal}', [DealController::class, 'destroy'])
                ->middleware(EnsureTenantFeatureEnabled::class.':crm')
                ->name('crm.deals.destroy');
            Route::post('/panel/crm/contacts', [ContactController::class, 'store'])
                ->middleware(EnsureTenantFeatureEnabled::class.':crm')
                ->name('crm.contacts.store');
            Route::get('/panel/crm/contacts/{contact}/edit', [ContactController::class, 'edit'])
                ->middleware(EnsureTenantFeatureEnabled::class.':crm')
                ->name('crm.contacts.edit');
            Route::patch('/panel/crm/contacts/{contact}', [ContactController::class, 'update'])
                ->middleware(EnsureTenantFeatureEnabled::class.':crm')
                ->name('crm.contacts.update');
            Route::delete('/panel/crm/contacts/{contact}', [ContactController::class, 'destroy'])
                ->middleware(EnsureTenantFeatureEnabled::class.':crm')
                ->name('crm.contacts.destroy');
            Route::get('/panel/files', [ModulePageController::class, 'files'])
                ->middleware(EnsureTenantFeatureEnabled::class.':files')
                ->name('files.index');
            Route::get('/panel/settings', [ModulePageController::class, 'settings'])->name('settings.index');
            Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
        });
    });
