<?php

namespace Tests\Feature\AdminConsole;

use App\Livewire\Landlord\Tenants\Show;
use App\Models\Landlord\AuditLog;
use App\Models\Landlord\Identity;
use App\Models\Landlord\Tenant;
use App\Models\Landlord\TenantFeature;
use App\Modules\Audit\Enums\AuditLogAction;
use App\Modules\Entitlements\Enums\SystemFeature;
use App\Modules\Entitlements\Enums\TenantFeatureSource;
use App\Modules\Identity\Enums\IdentityStatus;
use App\Modules\Tenancy\Enums\TenantBillingModel;
use App\Modules\Tenancy\Enums\TenantDeploymentType;
use App\Modules\Tenancy\Enums\TenantLicenseType;
use App\Modules\Tenancy\Enums\TenantStatus;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;
use Tests\TestCase;

final class AdminFeatureManagementTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh', [
            '--database' => 'sqlite',
            '--path' => 'database/migrations/landlord',
        ]);
    }

    public function test_superadmin_can_set_tenant_feature_access_from_tenant_screen(): void
    {
        $superadmin = $this->superadmin();
        $tenant = $this->tenant();

        $this->actingAs($superadmin, 'landlord');

        Livewire::test(Show::class, ['tenant' => $tenant])
            ->set('features.'.SystemFeature::Cms->value, true)
            ->set('features.'.SystemFeature::Crm->value, true)
            ->set('features.'.SystemFeature::Files->value, false)
            ->set('featureReason', 'Commercial plan setup.')
            ->call('saveFeatures')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('tenant_features', [
            'tenant_id' => $tenant->id,
            'feature' => SystemFeature::Cms->value,
            'enabled' => true,
            'source' => TenantFeatureSource::Manual->value,
            'reason' => 'Commercial plan setup.',
        ]);

        $this->assertDatabaseHas('tenant_features', [
            'tenant_id' => $tenant->id,
            'feature' => SystemFeature::Files->value,
            'enabled' => false,
            'source' => TenantFeatureSource::Manual->value,
        ]);

        $override = TenantFeature::query()
            ->where('tenant_id', $tenant->id)
            ->where('feature', SystemFeature::Cms->value)
            ->where('source', TenantFeatureSource::Manual->value)
            ->firstOrFail();

        $auditLog = AuditLog::query()
            ->where('action', AuditLogAction::TenantFeatureOverrideSet)
            ->where('subject_id', $override->id)
            ->firstOrFail();

        $this->assertNull($auditLog->before_json);
        $this->assertSame([
            'enabled' => true,
            'reason' => 'Commercial plan setup.',
            'source' => TenantFeatureSource::Manual->value,
        ], $auditLog->after_json);
        $this->assertSame(SystemFeature::Cms->value, $auditLog->metadata_json['feature_key']);
    }

    public function test_landlord_features_route_no_longer_exists(): void
    {
        $this->actingAs($this->superadmin(), 'landlord');

        $this->get('http://admin.aegoryx.test/features')->assertNotFound();
    }

    private function superadmin(): Identity
    {
        return Identity::query()->create([
            'email' => 'admin@example.test',
            'is_super_admin' => true,
            'status' => IdentityStatus::Active,
        ]);
    }

    private function tenant(): Tenant
    {
        return Tenant::query()->create([
            'name' => 'Example Tenant',
            'slug' => 'example-tenant',
            'schema_name' => 'tenant_example',
            'status' => TenantStatus::Active,
            'deployment_type' => TenantDeploymentType::Saas,
            'billing_model' => TenantBillingModel::Subscription,
            'license_type' => TenantLicenseType::SaasSubscription,
        ]);
    }
}
