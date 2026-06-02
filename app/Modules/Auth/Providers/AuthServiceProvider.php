<?php

namespace App\Modules\Auth\Providers;

use App\Models\Tenant\CrmContact;
use App\Modules\Crm\Policies\CrmContactPolicy;
use App\Support\Modules\ModuleServiceProvider;
use Illuminate\Support\Facades\Gate;

final class AuthServiceProvider extends ModuleServiceProvider
{
    public function boot(): void
    {
        Gate::policy(CrmContact::class, CrmContactPolicy::class);

        $this->loadModuleRoutes();
    }

    protected function moduleBasePath(): string
    {
        return dirname(__DIR__);
    }
}
