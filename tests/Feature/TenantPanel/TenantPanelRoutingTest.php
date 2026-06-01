<?php

namespace Tests\Feature\TenantPanel;

use App\Models\Landlord\Tenant;
use App\Models\Landlord\TenantDomain;
use App\Modules\Tenancy\Enums\TenantBillingModel;
use App\Modules\Tenancy\Enums\TenantDeploymentType;
use App\Modules\Tenancy\Enums\TenantDomainStatus;
use App\Modules\Tenancy\Enums\TenantDomainType;
use App\Modules\Tenancy\Enums\TenantLicenseType;
use App\Modules\Tenancy\Enums\TenantStatus;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

final class TenantPanelRoutingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh', [
            '--database' => 'sqlite',
            '--path' => 'database/migrations/landlord',
        ]);
    }

    public function test_tenant_panel_resolves_tenant_from_active_domain(): void
    {
        $tenant = $this->tenant();

        TenantDomain::query()->create([
            'tenant_id' => $tenant->id,
            'domain' => 'acme.aegoryx.test',
            'type' => TenantDomainType::Primary,
            'status' => TenantDomainStatus::Verified,
        ]);

        $this
            ->get('http://acme.aegoryx.test/panel')
            ->assertOk()
            ->assertSee('Tenant panel')
            ->assertSee('Active tenant')
            ->assertSee('Tenant user')
            ->assertSee('CMS')
            ->assertSee('CRM')
            ->assertSee($tenant->name)
            ->assertSee($tenant->slug)
            ->assertDontSee($tenant->schema_name);
    }

    public function test_tenant_panel_rejects_unknown_domain(): void
    {
        $this
            ->get('http://unknown.aegoryx.test/panel')
            ->assertNotFound();
    }

    private function tenant(): Tenant
    {
        return Tenant::query()->create([
            'name' => 'Acme Tenant',
            'slug' => 'acme',
            'schema_name' => 'tenant_acme',
            'status' => TenantStatus::Active,
            'deployment_type' => TenantDeploymentType::Saas,
            'billing_model' => TenantBillingModel::Subscription,
            'license_type' => TenantLicenseType::SaasSubscription,
        ]);
    }
}
