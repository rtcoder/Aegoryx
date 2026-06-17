<?php

namespace Tests\Feature\TenantPanel;

use App\Models\Landlord\License;
use App\Models\Landlord\Plan;
use App\Models\Landlord\Subscription;
use App\Models\Landlord\Tenant;
use App\Models\Landlord\TenantDomain;
use App\Models\Tenant\User;
use App\Modules\Billing\Enums\PlanStatus;
use App\Modules\Billing\Enums\SubscriptionStatus;
use App\Modules\Identity\Enums\TenantUserRole;
use App\Modules\Licensing\Enums\LicenseStatus;
use App\Modules\Tenancy\Enums\TenantBillingModel;
use App\Modules\Tenancy\Enums\TenantDeploymentType;
use App\Modules\Tenancy\Enums\TenantDomainStatus;
use App\Modules\Tenancy\Enums\TenantDomainType;
use App\Modules\Tenancy\Enums\TenantLicenseType;
use App\Modules\Tenancy\Enums\TenantStatus;
use App\Support\Localization\Locale;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

final class TenantSettingsTest extends TestCase
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
    }

    public function test_owner_can_update_tenant_default_locale(): void
    {
        $tenant = $this->tenant();
        $this->domain($tenant);
        $owner = $this->user(TenantUserRole::Owner, 'owner@example.test');
        $this->actingAs($owner, 'web');

        $this
            ->get('http://acme.aegoryx.test/panel/settings')
            ->assertOk()
            ->assertSee(__('tenant_settings.title'))
            ->assertSee(__('tenant_settings.default_locale'));

        $this
            ->patch('http://acme.aegoryx.test/panel/settings', [
                'locale' => Locale::German->value,
            ])
            ->assertRedirect('http://acme.aegoryx.test/panel/settings')
            ->assertSessionHas('success', __('tenant_settings.updated'));

        $this->assertSame(Locale::German, $tenant->refresh()->locale);
        $this->assertSame($owner->id, $tenant->updated_by);
    }

    public function test_viewer_cannot_update_tenant_settings(): void
    {
        $tenant = $this->tenant();
        $this->domain($tenant);
        $this->actingAs($this->user(TenantUserRole::Viewer, 'viewer@example.test'), 'web');

        $this
            ->get('http://acme.aegoryx.test/panel/settings')
            ->assertOk()
            ->assertSee(__('tenant_settings.read_only'));

        $this
            ->patch('http://acme.aegoryx.test/panel/settings', [
                'locale' => Locale::French->value,
            ])
            ->assertForbidden();

        $this->assertSame(Locale::Polish, $tenant->refresh()->locale);
    }

    public function test_owner_can_request_alias_domain(): void
    {
        $tenant = $this->tenant();
        $this->domain($tenant);
        $owner = $this->user(TenantUserRole::Owner, 'owner@example.test');
        $this->actingAs($owner, 'web');

        $this
            ->post('http://acme.aegoryx.test/panel/settings/domains', [
                'domain' => 'https://Portal.Example.test/some/path',
            ])
            ->assertRedirect('http://acme.aegoryx.test/panel/settings')
            ->assertSessionHas('success', __('tenant_settings.domain_requested'));

        $this->assertDatabaseHas('tenant_domains', [
            'tenant_id' => $tenant->id,
            'domain' => 'portal.example.test',
            'type' => TenantDomainType::Alias->value,
            'status' => TenantDomainStatus::Pending->value,
            'created_by' => $owner->id,
        ]);

        $this
            ->get('http://acme.aegoryx.test/panel/settings')
            ->assertOk()
            ->assertSee('portal.example.test')
            ->assertSee(__('tenant_settings.domain_statuses.pending'));
    }

    public function test_viewer_cannot_request_alias_domain(): void
    {
        $tenant = $this->tenant();
        $this->domain($tenant);
        $this->actingAs($this->user(TenantUserRole::Viewer, 'viewer@example.test'), 'web');

        $this
            ->post('http://acme.aegoryx.test/panel/settings/domains', [
                'domain' => 'portal.example.test',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('tenant_domains', [
            'domain' => 'portal.example.test',
        ]);
    }

    public function test_settings_show_read_only_billing_and_license_summary(): void
    {
        $tenant = $this->tenant();
        $this->domain($tenant);
        $this->actingAs($this->user(TenantUserRole::Owner, 'owner@example.test'), 'web');

        $plan = Plan::query()->create([
            'key' => 'pro',
            'name' => 'Pro',
            'status' => PlanStatus::Active,
        ]);

        Subscription::query()->create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'provider' => 'paddle',
            'status' => SubscriptionStatus::Active,
            'current_period_ends_at' => '2026-07-31 12:00:00',
        ]);

        License::query()->create([
            'tenant_id' => $tenant->id,
            'license_key_hash' => hash('sha256', 'license-key'),
            'status' => LicenseStatus::Active,
            'expires_at' => '2026-12-31 12:00:00',
            'last_verified_at' => '2026-06-17 12:00:00',
        ]);

        $this
            ->get('http://acme.aegoryx.test/panel/settings')
            ->assertOk()
            ->assertSee(__('tenant_settings.billing_title'))
            ->assertSee('Pro')
            ->assertSee(SubscriptionStatus::Active->value)
            ->assertSee(LicenseStatus::Active->value)
            ->assertSee('2026-07-31')
            ->assertSee('2026-12-31');
    }

    private function tenant(): Tenant
    {
        return Tenant::query()->create([
            'name' => 'Acme Tenant',
            'slug' => 'acme',
            'schema_name' => 'tenant_acme',
            'status' => TenantStatus::Active,
            'locale' => Locale::Polish,
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
