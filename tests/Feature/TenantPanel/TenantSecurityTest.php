<?php

namespace Tests\Feature\TenantPanel;

use App\Livewire\Tenant\Security\TwoFactorSettings;
use App\Models\Landlord\Tenant;
use App\Models\Landlord\TenantDomain;
use App\Models\Tenant\User;
use App\Modules\Identity\Enums\TenantUserRole;
use App\Modules\Identity\Support\TwoFactorAuthenticator;
use App\Modules\Tenancy\Enums\TenantBillingModel;
use App\Modules\Tenancy\Enums\TenantDeploymentType;
use App\Modules\Tenancy\Enums\TenantDomainStatus;
use App\Modules\Tenancy\Enums\TenantDomainType;
use App\Modules\Tenancy\Enums\TenantLicenseType;
use App\Modules\Tenancy\Enums\TenantStatus;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;
use Tests\TestCase;

final class TenantSecurityTest extends TestCase
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

    public function test_security_page_renders_and_user_can_enable_and_disable_two_factor(): void
    {
        $tenant = $this->tenant();
        $this->domain($tenant);
        $user = $this->user();
        $this->actingAs($user, 'web');

        $this
            ->get('http://acme.aegoryx.test/panel/security')
            ->assertOk()
            ->assertSee(__('two_factor.security_title'))
            ->assertSee(__('two_factor.tenant_2fa'));

        $component = Livewire::test(TwoFactorSettings::class)
            ->call('generate');

        $secret = $component->get('pendingSecret');

        $component
            ->set('code', app(TwoFactorAuthenticator::class)->currentCode($secret))
            ->call('enable')
            ->assertHasNoErrors();

        $this->assertTrue($user->refresh()->hasTwoFactorEnabled());

        Livewire::test(TwoFactorSettings::class)
            ->call('disable')
            ->assertHasNoErrors();

        $this->assertFalse($user->refresh()->hasTwoFactorEnabled());
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

    private function user(): User
    {
        return User::query()->create([
            'name' => 'Owner',
            'email' => 'owner@example.test',
            'password' => 'secret-password',
            'role' => TenantUserRole::Owner,
        ]);
    }
}
