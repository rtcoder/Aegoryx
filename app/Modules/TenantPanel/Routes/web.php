<?php

use App\Modules\Crm\Http\Controllers\CompanyController;
use App\Modules\Crm\Http\Controllers\ContactController;
use App\Modules\Crm\Http\Controllers\DealController;
use App\Modules\Crm\Http\Controllers\NoteController;
use App\Modules\Crm\Http\Controllers\TaskController;
use App\Modules\Entitlements\Enums\SystemFeature;
use App\Modules\Files\Http\Controllers\ActivityExportController;
use App\Modules\Files\Http\Controllers\FileController;
use App\Modules\TenantPanel\Http\Controllers\ActivityController;
use App\Modules\TenantPanel\Http\Controllers\Auth\LoginController;
use App\Modules\TenantPanel\Http\Controllers\CmsPageController;
use App\Modules\TenantPanel\Http\Controllers\DashboardController;
use App\Modules\TenantPanel\Http\Controllers\Modules\ModulePageController;
use App\Modules\TenantPanel\Http\Controllers\SecurityController;
use App\Modules\TenantPanel\Http\Controllers\UserController;
use App\Modules\TenantPanel\Http\Middleware\EnsureTenantAuthenticated;
use App\Modules\TenantPanel\Http\Middleware\EnsureTenantFeatureEnabled;
use App\Modules\TenantPanel\Http\Middleware\ResolveTenantFromDomain;
use Illuminate\Support\Facades\Route;

Route::middleware(ResolveTenantFromDomain::class)
    ->name('tenant.')
    ->group(function (): void {
        Route::get('/login', [LoginController::class, 'create'])->name('login');

        Route::middleware(EnsureTenantAuthenticated::class)->group(function (): void {
            Route::prefix('panel')->group(function (): void {
                Route::get('/', DashboardController::class)->name('dashboard');
                Route::get('cms', [CmsPageController::class, 'index'])
                    ->middleware(EnsureTenantFeatureEnabled::class.':'.SystemFeature::Cms->value)
                    ->name('cms.index');
                Route::prefix('files')
                    ->name('files.')
                    ->middleware(EnsureTenantFeatureEnabled::class.':'.SystemFeature::Files->value)
                    ->group(function (): void {
                        Route::get('/', [FileController::class, 'index'])->name('index');
                        Route::post('/', [FileController::class, 'store'])->name('store');
                        Route::post('exports/activity', [ActivityExportController::class, 'store'])->name('exports.activity.store');
                        Route::get('{file}/download', [FileController::class, 'download'])->name('download');
                        Route::delete('{file}', [FileController::class, 'destroy'])->name('destroy');
                    });
                Route::get('settings', [ModulePageController::class, 'settings'])->name('settings.index');
                Route::get('security', [SecurityController::class, 'index'])->name('security.index');
                Route::get('users', [UserController::class, 'index'])->name('users.index');
                Route::get('activity', [ActivityController::class, 'index'])->name('activity.index');

                Route::prefix('crm')
                    ->name('crm.')
                    ->middleware(EnsureTenantFeatureEnabled::class.':'.SystemFeature::Crm->value)
                    ->group(function (): void {
                        Route::get('/', [ContactController::class, 'index'])->name('index');

                        Route::prefix('contacts')->name('contacts.')->group(function (): void {
                            Route::post('/', [ContactController::class, 'store'])->name('store');
                            Route::get('{contact}/edit', [ContactController::class, 'edit'])->name('edit');
                            Route::patch('{contact}', [ContactController::class, 'update'])->name('update');
                            Route::delete('{contact}', [ContactController::class, 'destroy'])->name('destroy');
                        });

                        Route::prefix('companies')->name('companies.')->group(function (): void {
                            Route::get('/', [CompanyController::class, 'index'])->name('index');
                            Route::post('/', [CompanyController::class, 'store'])->name('store');
                            Route::get('{company}/edit', [CompanyController::class, 'edit'])->name('edit');
                            Route::patch('{company}', [CompanyController::class, 'update'])->name('update');
                            Route::delete('{company}', [CompanyController::class, 'destroy'])->name('destroy');
                        });

                        Route::prefix('deals')->name('deals.')->group(function (): void {
                            Route::get('/', [DealController::class, 'index'])->name('index');
                            Route::post('/', [DealController::class, 'store'])->name('store');
                            Route::get('{deal}/edit', [DealController::class, 'edit'])->name('edit');
                            Route::patch('{deal}', [DealController::class, 'update'])->name('update');
                            Route::delete('{deal}', [DealController::class, 'destroy'])->name('destroy');
                        });

                        Route::prefix('notes')->name('notes.')->group(function (): void {
                            Route::get('/', [NoteController::class, 'index'])->name('index');
                            Route::post('/', [NoteController::class, 'store'])->name('store');
                            Route::get('{note}/edit', [NoteController::class, 'edit'])->name('edit');
                            Route::patch('{note}', [NoteController::class, 'update'])->name('update');
                            Route::delete('{note}', [NoteController::class, 'destroy'])->name('destroy');
                        });

                        Route::prefix('tasks')->name('tasks.')->group(function (): void {
                            Route::get('/', [TaskController::class, 'index'])->name('index');
                            Route::post('/', [TaskController::class, 'store'])->name('store');
                            Route::get('{task}/edit', [TaskController::class, 'edit'])->name('edit');
                            Route::patch('{task}', [TaskController::class, 'update'])->name('update');
                            Route::delete('{task}', [TaskController::class, 'destroy'])->name('destroy');
                        });
                    });
            });

            Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
        });
    });
