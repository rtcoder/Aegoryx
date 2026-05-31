<?php

namespace Tests\Feature\AdminConsole;

use App\Models\Landlord\AuditLog;
use App\Models\Landlord\Feature;
use App\Models\Landlord\Identity;
use App\Models\Landlord\Tenant;
use App\Models\Landlord\TenantFeature;
use App\Modules\Entitlements\Enums\FeatureStatus;
use App\Modules\Entitlements\Enums\TenantFeatureSource;
use App\Modules\Identity\Enums\IdentityStatus;
use App\Modules\Tenancy\Enums\TenantBillingModel;
use App\Modules\Tenancy\Enums\TenantDeploymentType;
use App\Modules\Tenancy\Enums\TenantLicenseType;
use App\Modules\Tenancy\Enums\TenantStatus;
use Illuminate\Support\Facades\Artisan;
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

    public function test_superadmin_can_create_feature_through_entitlements(): void
    {
        $superadmin = $this->superadmin();
        $this->actingAs($superadmin, 'landlord');

        $this
            ->post('http://admin.aegoryx.test/features', [
                'key' => 'cms.pages',
                'name' => 'CMS Pages',
                'description' => 'Manage CMS pages.',
                'status' => FeatureStatus::Active->value,
            ])
            ->assertRedirect();

        $feature = Feature::query()->where('key', 'cms.pages')->firstOrFail();

        $this->assertSame('CMS Pages', $feature->name);
        $this->assertSame(FeatureStatus::Active, $feature->status);
        $this->assertSame($superadmin->id, $feature->created_by);

        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $superadmin->id,
            'subject_type' => Feature::class,
            'subject_id' => $feature->id,
            'action' => 'feature_created',
        ]);
    }

    public function test_superadmin_can_update_feature_status(): void
    {
        $superadmin = $this->superadmin();
        $this->actingAs($superadmin, 'landlord');

        $feature = $this->feature();

        $this
            ->patch("http://admin.aegoryx.test/features/{$feature->id}/status", [
                'status' => FeatureStatus::Disabled->value,
            ])
            ->assertRedirect("http://admin.aegoryx.test/features/{$feature->id}");

        $feature->refresh();

        $this->assertSame(FeatureStatus::Disabled, $feature->status);
        $this->assertSame($superadmin->id, $feature->updated_by);

        $auditLog = AuditLog::query()
            ->where('action', 'feature_status_changed')
            ->where('subject_id', $feature->id)
            ->firstOrFail();

        $this->assertSame(['status' => FeatureStatus::Active->value], $auditLog->before_json);
        $this->assertSame(['status' => FeatureStatus::Disabled->value], $auditLog->after_json);
    }

    public function test_superadmin_can_set_tenant_feature_override_with_reason(): void
    {
        $superadmin = $this->superadmin();
        $this->actingAs($superadmin, 'landlord');

        $feature = $this->feature();
        $tenant = $this->tenant();

        $this
            ->post("http://admin.aegoryx.test/features/{$feature->id}/tenant-overrides", [
                'tenant_id' => $tenant->id,
                'enabled' => '0',
                'reason' => 'Temporary commercial exception.',
            ])
            ->assertRedirect("http://admin.aegoryx.test/features/{$feature->id}");

        $override = TenantFeature::query()
            ->where('tenant_id', $tenant->id)
            ->where('feature_id', $feature->id)
            ->where('source', TenantFeatureSource::Manual->value)
            ->firstOrFail();

        $this->assertFalse($override->enabled);
        $this->assertSame('Temporary commercial exception.', $override->reason);
        $this->assertSame($superadmin->id, $override->created_by);
        $this->assertSame($superadmin->id, $override->updated_by);

        $auditLog = AuditLog::query()
            ->where('action', 'tenant_feature_override_set')
            ->where('subject_id', $override->id)
            ->firstOrFail();

        $this->assertNull($auditLog->before_json);
        $this->assertSame([
            'enabled' => false,
            'reason' => 'Temporary commercial exception.',
            'source' => TenantFeatureSource::Manual->value,
        ], $auditLog->after_json);
    }

    private function superadmin(): Identity
    {
        return Identity::query()->create([
            'email' => 'admin@example.test',
            'is_super_admin' => true,
            'status' => IdentityStatus::Active,
        ]);
    }

    private function feature(): Feature
    {
        return Feature::query()->create([
            'key' => 'crm.contacts',
            'name' => 'CRM Contacts',
            'status' => FeatureStatus::Active,
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
