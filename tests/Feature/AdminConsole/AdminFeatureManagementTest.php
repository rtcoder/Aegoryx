<?php

namespace Tests\Feature\AdminConsole;

use App\Livewire\Admin\Tenants\Show;
use App\Models\System\AuditLog;
use App\Models\System\Identity;
use App\Models\System\Plan;
use App\Models\System\PlanFeature;
use App\Models\System\Subscription;
use App\Models\System\Tenant;
use App\Models\System\TenantFeature;
use App\Modules\Audit\Enums\AuditLogAction;
use App\Modules\Billing\Enums\PlanStatus;
use App\Modules\Billing\Enums\SubscriptionStatus;
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
            '--path' => 'database/migrations/system',
        ]);
    }

    public function test_superadmin_can_set_tenant_feature_access_from_tenant_screen(): void
    {
        $superadmin = $this->superadmin();
        $tenant = $this->tenant();

        $this->actingAs($superadmin, 'admin');

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

        $this->assertDatabaseMissing('tenant_features', [
            'tenant_id' => $tenant->id,
            'feature' => SystemFeature::Files->value,
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

    public function test_admin_features_route_no_longer_exists(): void
    {
        $this->actingAs($this->superadmin(), 'admin');

        $this->get('http://admin.aegoryx.test/features')->assertNotFound();
    }

    public function test_tenant_feature_screen_uses_effective_entitlements_from_active_plan(): void
    {
        $this->actingAs($this->superadmin(), 'admin');

        $tenant = $this->tenant();
        $plan = Plan::query()->create([
            'key' => 'growth',
            'name' => 'Growth',
            'status' => PlanStatus::Active,
        ]);
        PlanFeature::query()->create([
            'plan_id' => $plan->id,
            'feature' => SystemFeature::Cms,
            'enabled' => true,
        ]);
        Subscription::query()->create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'provider' => 'manual',
            'status' => SubscriptionStatus::Active,
        ]);

        Livewire::test(Show::class, ['tenant' => $tenant])
            ->assertSet('features.'.SystemFeature::Cms->value, true)
            ->assertSet('features.'.SystemFeature::Crm->value, false)
            ->assertSee(__('features.source_labels.plan'))
            ->assertSee('plan:growth');
    }

    public function test_saving_unchanged_effective_plan_features_does_not_create_manual_overrides(): void
    {
        $this->actingAs($this->superadmin(), 'admin');

        $tenant = $this->tenant();
        $plan = Plan::query()->create([
            'key' => 'growth',
            'name' => 'Growth',
            'status' => PlanStatus::Active,
        ]);
        PlanFeature::query()->create([
            'plan_id' => $plan->id,
            'feature' => SystemFeature::Cms,
            'enabled' => true,
        ]);
        Subscription::query()->create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'provider' => 'manual',
            'status' => SubscriptionStatus::Active,
        ]);

        Livewire::test(Show::class, ['tenant' => $tenant])
            ->call('saveFeatures')
            ->assertHasNoErrors();

        $this->assertSame(0, TenantFeature::query()->where('tenant_id', $tenant->id)->count());
    }

    public function test_superadmin_can_clear_manual_feature_override(): void
    {
        $this->actingAs($this->superadmin(), 'admin');

        $tenant = $this->tenant();
        $override = TenantFeature::query()->create([
            'tenant_id' => $tenant->id,
            'feature' => SystemFeature::Cms,
            'enabled' => false,
            'source' => TenantFeatureSource::Manual,
            'reason' => 'Temporary block.',
        ]);

        Livewire::test(Show::class, ['tenant' => $tenant])
            ->assertSee(__('features.clear_override'))
            ->call('clearFeatureOverride', SystemFeature::Cms->value)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('tenant_features', [
            'id' => $override->id,
        ]);
        $auditLog = AuditLog::query()
            ->where('action', AuditLogAction::TenantFeatureOverrideCleared)
            ->where('subject_id', $override->id)
            ->firstOrFail();

        $this->assertSame([
            'enabled' => false,
            'reason' => 'Temporary block.',
            'source' => TenantFeatureSource::Manual->value,
        ], $auditLog->before_json);
        $this->assertNull($auditLog->after_json);
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
