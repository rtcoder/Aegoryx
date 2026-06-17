<?php

namespace Tests\Feature\TenantPanel;

use App\Models\Landlord\Tenant;
use App\Models\Landlord\TenantDomain;
use App\Models\Tenant\ActivityEntry;
use App\Models\Tenant\User;
use App\Modules\Audit\Enums\ActivityEntryAction;
use App\Modules\Identity\Enums\TenantUserRole;
use App\Modules\Tenancy\Enums\TenantBillingModel;
use App\Modules\Tenancy\Enums\TenantDeploymentType;
use App\Modules\Tenancy\Enums\TenantDomainStatus;
use App\Modules\Tenancy\Enums\TenantDomainType;
use App\Modules\Tenancy\Enums\TenantLicenseType;
use App\Modules\Tenancy\Enums\TenantStatus;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

final class TenantActivityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh', [
            '--database' => 'sqlite',
            '--path' => 'database/migrations/landlord',
        ]);

        Artisan::call('migrate', [
            '--database' => 'sqlite',
            '--path' => 'database/migrations/tenant',
        ]);

        $this->domain($this->tenant());
    }

    public function test_owner_can_view_tenant_activity_entries(): void
    {
        $owner = $this->user(TenantUserRole::Owner, 'owner@example.test');
        $this->actingAs($owner, 'web');

        ActivityEntry::query()->create([
            'actor_type' => User::class,
            'actor_id' => $owner->id,
            'subject_type' => User::class,
            'subject_id' => $owner->id,
            'action' => ActivityEntryAction::TenantTwoFactorEnabled,
            'description' => 'Tenant 2FA enabled for owner.',
            'ip' => '127.0.0.1',
        ]);

        $this
            ->get('http://acme.aegoryx.test/panel/activity')
            ->assertOk()
            ->assertSee(__('audit_view.activity_title'))
            ->assertSee(ActivityEntryAction::TenantTwoFactorEnabled->value)
            ->assertSee('Tenant 2FA enabled for owner.');
    }

    public function test_viewer_cannot_view_tenant_activity_entries(): void
    {
        $viewer = $this->user(TenantUserRole::Viewer, 'viewer@example.test');
        $this->actingAs($viewer, 'web');

        $this
            ->get('http://acme.aegoryx.test/panel/activity')
            ->assertForbidden();
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

    private function user(TenantUserRole $role, string $email): User
    {
        return User::query()->create([
            'name' => ucfirst($role->value),
            'email' => $email,
            'password' => 'secret-password',
            'role' => $role,
        ]);
    }
}
