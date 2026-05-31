<?php

namespace Tests\Feature\AdminConsole;

use App\Models\Landlord\AuditLog;
use App\Models\Landlord\Identity;
use App\Models\Landlord\Tenant;
use App\Modules\Identity\Enums\IdentityStatus;
use App\Modules\Tenancy\Enums\TenantBillingModel;
use App\Modules\Tenancy\Enums\TenantDeploymentType;
use App\Modules\Tenancy\Enums\TenantLicenseType;
use App\Modules\Tenancy\Enums\TenantStatus;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

final class AdminNavigationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh', [
            '--database' => 'sqlite',
            '--path' => 'database/migrations/landlord',
        ]);
    }

    public function test_guest_is_redirected_to_landlord_login(): void
    {
        $this
            ->get('http://admin.aegoryx.test/')
            ->assertRedirect('http://admin.aegoryx.test/login');
    }

    public function test_superadmin_can_see_admin_navigation_pages(): void
    {
        $this->actingAs($this->superadmin(), 'landlord');

        foreach ([
            'http://admin.aegoryx.test/',
            'http://admin.aegoryx.test/tenants',
            'http://admin.aegoryx.test/features',
            'http://admin.aegoryx.test/licenses',
            'http://admin.aegoryx.test/billing',
            'http://admin.aegoryx.test/support',
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_superadmin_can_list_tenants(): void
    {
        $this->actingAs($this->superadmin(), 'landlord');

        $tenant = $this->tenant([
            'name' => 'Acme Labs',
            'slug' => 'acme-labs',
            'schema_name' => 'tenant_acme_labs',
        ]);

        $this
            ->get('http://admin.aegoryx.test/tenants')
            ->assertOk()
            ->assertSee($tenant->name)
            ->assertSee($tenant->schema_name)
            ->assertSee(TenantStatus::Active->value);
    }

    public function test_superadmin_can_view_tenant_details(): void
    {
        $this->actingAs($this->superadmin(), 'landlord');

        $tenant = $this->tenant();

        $this
            ->get("http://admin.aegoryx.test/tenants/{$tenant->id}")
            ->assertOk()
            ->assertSee($tenant->name)
            ->assertSee($tenant->slug)
            ->assertSee($tenant->schema_name);
    }

    public function test_superadmin_can_update_tenant_status_and_audit_it(): void
    {
        $superadmin = $this->superadmin();
        $this->actingAs($superadmin, 'landlord');

        $tenant = $this->tenant();

        $this
            ->patch("http://admin.aegoryx.test/tenants/{$tenant->id}/status", [
                'status' => TenantStatus::Suspended->value,
            ])
            ->assertRedirect("http://admin.aegoryx.test/tenants/{$tenant->id}");

        $tenant->refresh();

        $this->assertSame(TenantStatus::Suspended, $tenant->status);
        $this->assertSame($superadmin->id, $tenant->updated_by);

        $auditLog = AuditLog::query()
            ->where('action', 'tenant_status_changed')
            ->where('subject_id', $tenant->id)
            ->firstOrFail();

        $this->assertSame('superadmin', $auditLog->actor_type);
        $this->assertSame($superadmin->id, $auditLog->actor_id);
        $this->assertSame(['status' => TenantStatus::Active->value], $auditLog->before_json);
        $this->assertSame(['status' => TenantStatus::Suspended->value], $auditLog->after_json);
    }

    private function superadmin(): Identity
    {
        return Identity::query()->create([
            'email' => 'admin@example.test',
            'is_super_admin' => true,
            'status' => IdentityStatus::Active,
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function tenant(array $attributes = []): Tenant
    {
        return Tenant::query()->create(array_merge([
            'name' => 'Example Tenant',
            'slug' => 'example-tenant',
            'schema_name' => 'tenant_example',
            'status' => TenantStatus::Active,
            'deployment_type' => TenantDeploymentType::Saas,
            'billing_model' => TenantBillingModel::Subscription,
            'license_type' => TenantLicenseType::SaasSubscription,
        ], $attributes));
    }
}
