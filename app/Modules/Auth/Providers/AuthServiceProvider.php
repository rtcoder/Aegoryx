<?php

namespace App\Modules\Auth\Providers;

use App\Models\Tenant\ActivityEntry;
use App\Models\Tenant\CrmCompany;
use App\Models\Tenant\CrmContact;
use App\Models\Tenant\CrmDeal;
use App\Models\Tenant\CrmNote;
use App\Models\Tenant\CrmTask;
use App\Models\Tenant\TenantFile;
use App\Modules\Audit\Policies\ActivityEntryPolicy;
use App\Modules\Crm\Policies\CrmCompanyPolicy;
use App\Modules\Crm\Policies\CrmContactPolicy;
use App\Modules\Crm\Policies\CrmDealPolicy;
use App\Modules\Crm\Policies\CrmNotePolicy;
use App\Modules\Crm\Policies\CrmTaskPolicy;
use App\Modules\Files\Policies\TenantFilePolicy;
use App\Support\Modules\ModuleServiceProvider;
use Illuminate\Support\Facades\Gate;

final class AuthServiceProvider extends ModuleServiceProvider
{
    public function boot(): void
    {
        Gate::policy(CrmContact::class, CrmContactPolicy::class);
        Gate::policy(CrmCompany::class, CrmCompanyPolicy::class);
        Gate::policy(CrmDeal::class, CrmDealPolicy::class);
        Gate::policy(CrmNote::class, CrmNotePolicy::class);
        Gate::policy(CrmTask::class, CrmTaskPolicy::class);
        Gate::policy(TenantFile::class, TenantFilePolicy::class);
        Gate::policy(ActivityEntry::class, ActivityEntryPolicy::class);

        $this->loadModuleRoutes();
    }

    protected function moduleBasePath(): string
    {
        return dirname(__DIR__);
    }
}
