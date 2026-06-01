<?php

namespace Tests\Feature\TenantPanel;

use App\Models\Landlord\Feature;
use App\Models\Landlord\Tenant;
use App\Models\Landlord\TenantDomain;
use App\Models\Landlord\TenantFeature;
use App\Modules\Entitlements\Enums\FeatureStatus;
use App\Modules\Entitlements\Enums\TenantFeatureSource;
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
        $this->domain($tenant);
        $this->feature('cms');
        $this->feature('crm');
        $this->feature('files');
        $this->manualOverride($tenant, 'cms', true, ['secret_limit' => 'do-not-render']);
        $this->manualOverride($tenant, 'crm', false);

        $response = $this
            ->get('http://acme.aegoryx.test/panel')
            ->assertOk()
            ->assertSee('Panel tenanta')
            ->assertSee('Aktywny tenant')
            ->assertSee('Użytkownik tenanta')
            ->assertSee('CMS')
            ->assertSee($tenant->name)
            ->assertSee($tenant->slug)
            ->assertDontSee($tenant->schema_name);

        $response
            ->assertSee('/panel/cms', false)
            ->assertDontSee('/panel/crm', false)
            ->assertDontSee('do-not-render');
    }

    public function test_tenant_panel_rejects_unknown_domain(): void
    {
        $this
            ->get('http://unknown.aegoryx.test/panel')
            ->assertNotFound();
    }

    public function test_enabled_module_route_renders(): void
    {
        $tenant = $this->tenant();
        $this->domain($tenant);
        $this->feature('cms');
        $this->manualOverride($tenant, 'cms', true);

        $this
            ->get('http://acme.aegoryx.test/panel/cms')
            ->assertOk()
            ->assertSee('CMS')
            ->assertSee('Miejsce na implementację modułu.');
    }

    public function test_disabled_module_route_returns_403(): void
    {
        $tenant = $this->tenant();
        $this->domain($tenant);
        $this->feature('crm');
        $this->manualOverride($tenant, 'crm', false);

        $this
            ->get('http://acme.aegoryx.test/panel/crm')
            ->assertForbidden()
            ->assertSee('Dostęp niedostępny')
            ->assertSee('Ten moduł nie jest dostępny dla aktywnego tenanta.');
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

    private function domain(Tenant $tenant): TenantDomain
    {
        return TenantDomain::query()->create([
            'tenant_id' => $tenant->id,
            'domain' => 'acme.aegoryx.test',
            'type' => TenantDomainType::Primary,
            'status' => TenantDomainStatus::Verified,
        ]);
    }

    private function feature(string $key): Feature
    {
        return Feature::query()->create([
            'key' => $key,
            'name' => strtoupper($key),
            'status' => FeatureStatus::Active,
        ]);
    }

    /**
     * @param  array<string, mixed>  $config
     */
    private function manualOverride(Tenant $tenant, string $featureKey, bool $enabled, array $config = []): TenantFeature
    {
        $feature = Feature::query()->where('key', $featureKey)->firstOrFail();

        return TenantFeature::query()->create([
            'tenant_id' => $tenant->id,
            'feature_id' => $feature->id,
            'enabled' => $enabled,
            'source' => TenantFeatureSource::Manual,
            'reason' => 'Test entitlement.',
            'config' => $config,
        ]);
    }
}
