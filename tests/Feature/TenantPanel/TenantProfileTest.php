<?php

namespace Tests\Feature\TenantPanel;

use App\Models\Landlord\Tenant;
use App\Models\Landlord\TenantDomain;
use App\Models\Tenant\User;
use App\Modules\Identity\Enums\TenantUserRole;
use App\Modules\Tenancy\Enums\TenantBillingModel;
use App\Modules\Tenancy\Enums\TenantDeploymentType;
use App\Modules\Tenancy\Enums\TenantDomainStatus;
use App\Modules\Tenancy\Enums\TenantDomainType;
use App\Modules\Tenancy\Enums\TenantLicenseType;
use App\Modules\Tenancy\Enums\TenantStatus;
use App\Support\Localization\Locale;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

final class TenantProfileTest extends TestCase
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

    public function test_user_can_update_own_profile_locale(): void
    {
        $user = User::query()->create([
            'name' => 'Member',
            'email' => 'member@example.test',
            'password' => 'secret-password',
            'role' => TenantUserRole::Member,
            'locale' => Locale::Polish,
        ]);
        $this->actingAs($user, 'web');

        $this
            ->get('http://acme.aegoryx.test/panel/profile')
            ->assertOk()
            ->assertSee(__('tenant_profile.title'));

        $this
            ->patch('http://acme.aegoryx.test/panel/profile', [
                'name' => 'Updated Member',
                'locale' => Locale::French->value,
            ])
            ->assertRedirect('http://acme.aegoryx.test/panel/profile')
            ->assertSessionHas('success', __('tenant_profile.updated'));

        $user->refresh();

        $this->assertSame('Updated Member', $user->name);
        $this->assertSame(Locale::French, $user->locale);
        $this->assertSame($user->id, $user->updated_by);
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
}
