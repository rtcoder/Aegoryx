<?php

namespace Tests\Feature\Entitlements;

use App\Models\Landlord\License;
use App\Models\Landlord\Plan;
use App\Models\Landlord\PlanFeature;
use App\Models\Landlord\Subscription;
use App\Models\Landlord\Tenant;
use App\Models\Landlord\TenantFeature;
use App\Modules\Billing\Enums\PlanStatus;
use App\Modules\Billing\Enums\SubscriptionStatus;
use App\Modules\Entitlements\Enums\SystemFeature;
use App\Modules\Entitlements\Enums\TenantFeatureSource;
use App\Modules\Entitlements\Services\EffectiveEntitlements;
use App\Modules\Licensing\Enums\LicenseStatus;
use App\Modules\Tenancy\Enums\TenantBillingModel;
use App\Modules\Tenancy\Enums\TenantDeploymentType;
use App\Modules\Tenancy\Enums\TenantLicenseType;
use App\Modules\Tenancy\Enums\TenantStatus;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

final class EffectiveEntitlementsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh', [
            '--database' => 'sqlite',
            '--path' => 'database/migrations/landlord',
        ]);
    }

    public function test_active_subscription_plan_grants_features_limits_and_config(): void
    {
        $tenant = $this->tenant();
        $plan = Plan::query()->create([
            'key' => 'growth',
            'name' => 'Growth',
            'status' => PlanStatus::Active,
            'limits' => ['crm.contacts' => 500],
        ]);
        PlanFeature::query()->create([
            'plan_id' => $plan->id,
            'feature' => SystemFeature::Crm,
            'enabled' => true,
            'config' => ['pipeline_count' => 3],
        ]);
        Subscription::query()->create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'provider' => 'paddle',
            'status' => SubscriptionStatus::Active,
        ]);

        $entitlements = app(EffectiveEntitlements::class);

        $this->assertTrue($entitlements->allows($tenant, SystemFeature::Crm));
        $this->assertFalse($entitlements->allows($tenant, SystemFeature::Cms));
        $this->assertSame(500, $entitlements->limit($tenant, 'crm.contacts'));
        $this->assertSame(3, $entitlements->config($tenant, SystemFeature::Crm, 'pipeline_count'));
        $this->assertSame(TenantFeatureSource::Plan->value, $entitlements->forTenant($tenant)[SystemFeature::Crm->value]['source']);
    }

    public function test_active_license_payload_can_grant_features_without_subscription(): void
    {
        $tenant = $this->tenant();
        License::query()->create([
            'tenant_id' => $tenant->id,
            'license_key_hash' => hash('sha256', 'license-key'),
            'status' => LicenseStatus::Active,
            'payload' => [
                'features' => [
                    SystemFeature::Cms->value => [
                        'enabled' => true,
                        'config' => ['max_pages' => 25],
                    ],
                    SystemFeature::Files->value,
                ],
                'limits' => ['cms.pages' => 25],
            ],
        ]);

        $entitlements = app(EffectiveEntitlements::class);

        $this->assertTrue($entitlements->allows($tenant, SystemFeature::Cms));
        $this->assertTrue($entitlements->allows($tenant, SystemFeature::Files));
        $this->assertFalse($entitlements->allows($tenant, SystemFeature::Crm));
        $this->assertSame(25, $entitlements->limit($tenant, 'cms.pages'));
        $this->assertSame(25, $entitlements->config($tenant, SystemFeature::Cms, 'max_pages'));
        $this->assertSame(TenantFeatureSource::License->value, $entitlements->forTenant($tenant)[SystemFeature::Cms->value]['source']);
    }

    public function test_manual_override_has_highest_priority_and_can_override_limits(): void
    {
        $tenant = $this->tenant();
        $plan = Plan::query()->create([
            'key' => 'starter',
            'name' => 'Starter',
            'status' => PlanStatus::Active,
            'limits' => ['crm.contacts' => 100],
        ]);
        PlanFeature::query()->create([
            'plan_id' => $plan->id,
            'feature' => SystemFeature::Crm,
            'enabled' => true,
        ]);
        Subscription::query()->create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'provider' => 'paddle',
            'status' => SubscriptionStatus::Active,
        ]);
        TenantFeature::query()->create([
            'tenant_id' => $tenant->id,
            'feature' => SystemFeature::Crm,
            'enabled' => false,
            'source' => TenantFeatureSource::Manual,
            'reason' => 'Manual suspension.',
            'config' => [
                'limits' => ['crm.contacts' => 0],
            ],
        ]);

        $entitlements = app(EffectiveEntitlements::class);
        $effective = $entitlements->forTenant($tenant)[SystemFeature::Crm->value];

        $this->assertFalse($entitlements->allows($tenant, SystemFeature::Crm));
        $this->assertSame(0, $entitlements->limit($tenant, 'crm.contacts'));
        $this->assertSame(TenantFeatureSource::Manual->value, $effective['source']);
        $this->assertSame('Manual suspension.', $effective['reason']);
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
