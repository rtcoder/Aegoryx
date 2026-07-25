<?php

namespace Tests\Feature\AdminConsole;

use App\Models\System\AuditLog;
use App\Models\System\Identity;
use App\Models\System\License;
use App\Models\System\Tenant;
use App\Modules\Audit\Enums\AuditLogAction;
use App\Modules\Identity\Enums\IdentityStatus;
use App\Modules\Licensing\Enums\LicenseStatus;
use App\Modules\Tenancy\Enums\TenantBillingModel;
use App\Modules\Tenancy\Enums\TenantDeploymentType;
use App\Modules\Tenancy\Enums\TenantLicenseType;
use App\Modules\Tenancy\Enums\TenantStatus;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

final class AdminLicenseManagementTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh', [
            '--database' => 'sqlite',
            '--path' => 'database/migrations/system',
        ]);
    }

    public function test_superadmin_can_view_licenses(): void
    {
        $this->actingAs($this->superadmin(), 'admin');

        $tenant = $this->tenant();
        $license = $this->license([
            'tenant_id' => $tenant->id,
            'status' => LicenseStatus::Active,
        ]);

        $this
            ->get('http://admin.aegoryx.test/licenses')
            ->assertOk()
            ->assertSee($tenant->name)
            ->assertSee($license->type)
            ->assertSee(LicenseStatus::Active->value);
    }

    public function test_superadmin_can_verify_expired_license_without_logging_secret(): void
    {
        $superadmin = $this->superadmin();
        $this->actingAs($superadmin, 'admin');

        $license = $this->license([
            'status' => LicenseStatus::Active,
            'expires_at' => now()->subDay(),
            'payload' => [
                'grace_until' => now()->subHour()->toISOString(),
                'secret' => 'do-not-log-this',
            ],
        ]);

        $this
            ->post("http://admin.aegoryx.test/licenses/{$license->id}/verify")
            ->assertRedirect("http://admin.aegoryx.test/licenses/{$license->id}");

        $license->refresh();

        $this->assertSame(LicenseStatus::Expired, $license->status);
        $this->assertNotNull($license->last_verified_at);
        $this->assertSame($superadmin->id, $license->updated_by);

        $auditLog = AuditLog::query()
            ->where('action', AuditLogAction::LicenseVerified)
            ->where('subject_id', $license->id)
            ->firstOrFail();

        $this->assertSame(['status' => LicenseStatus::Active->value, 'last_verified_at' => null], $auditLog->before_json);
        $this->assertSame(LicenseStatus::Expired->value, $auditLog->after_json['status']);
        $this->assertStringNotContainsString('do-not-log-this', json_encode($auditLog->toArray()));
    }

    public function test_verify_maps_expired_license_to_grace_when_grace_until_is_future(): void
    {
        $this->actingAs($this->superadmin(), 'admin');

        $license = $this->license([
            'status' => LicenseStatus::Active,
            'expires_at' => now()->subDay(),
            'payload' => ['grace_until' => now()->addDays(3)->toISOString()],
        ]);

        $this->post("http://admin.aegoryx.test/licenses/{$license->id}/verify");

        $this->assertSame(LicenseStatus::Grace, $license->refresh()->status);
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

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function license(array $attributes = []): License
    {
        return License::query()->create(array_merge([
            'license_key_hash' => hash('sha256', 'example-license-key'),
            'type' => 'self_hosted_subscription',
            'status' => LicenseStatus::Inactive,
            'issued_at' => now()->subMonth(),
            'expires_at' => now()->addMonth(),
        ], $attributes));
    }
}
