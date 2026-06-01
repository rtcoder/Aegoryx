<?php

namespace Tests\Feature\Localization;

use App\Models\Landlord\Identity;
use App\Models\Landlord\Tenant;
use App\Models\Landlord\TenantDomain;
use App\Models\Tenant\User;
use App\Modules\Identity\Enums\IdentityStatus;
use App\Modules\Tenancy\Enums\TenantBillingModel;
use App\Modules\Tenancy\Enums\TenantDeploymentType;
use App\Modules\Tenancy\Enums\TenantDomainStatus;
use App\Modules\Tenancy\Enums\TenantDomainType;
use App\Modules\Tenancy\Enums\TenantLicenseType;
use App\Modules\Tenancy\Enums\TenantStatus;
use App\Support\Localization\Locale;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

final class LocalePersistenceTest extends TestCase
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

    public function test_supported_locales_are_explicit_and_extendable(): void
    {
        $this->assertSame(['pl', 'en', 'de', 'es', 'ru', 'fr'], Locale::values());
        $this->assertSame(Locale::values(), config('aegoryx.localization.supported_locales'));
    }

    public function test_identity_and_tenant_have_locale(): void
    {
        $identity = Identity::query()->create([
            'email' => 'admin@example.test',
            'status' => IdentityStatus::Active,
            'locale' => Locale::English,
        ]);

        $tenant = $this->tenant(Locale::German);

        $this->assertSame(Locale::English, $identity->refresh()->locale);
        $this->assertSame(Locale::German, $tenant->refresh()->locale);
    }

    public function test_tenant_user_inherits_locale_from_tenant_but_can_override_it(): void
    {
        $tenant = $this->tenant(Locale::German);
        request()->attributes->set('tenant', $tenant);

        $inheritedUser = User::query()->create([
            'name' => 'Inherited User',
            'email' => 'inherited@example.test',
            'password' => 'secret-password',
        ]);

        $customUser = User::query()->create([
            'name' => 'Custom User',
            'email' => 'custom@example.test',
            'password' => 'secret-password',
            'locale' => Locale::French,
        ]);

        $this->assertSame(Locale::German, $inheritedUser->refresh()->locale);
        $this->assertSame(Locale::French, $customUser->refresh()->locale);
    }

    public function test_landlord_request_uses_authenticated_identity_locale(): void
    {
        $identity = Identity::query()->create([
            'email' => 'admin@example.test',
            'is_super_admin' => true,
            'status' => IdentityStatus::Active,
            'locale' => Locale::German,
        ]);

        $this->actingAs($identity, 'landlord');

        $this
            ->get('http://admin.aegoryx.test/')
            ->assertOk()
            ->assertSee('<html lang="de">', false)
            ->assertSee('Landlord-Dashboard');
    }

    public function test_tenant_panel_uses_tenant_locale_before_user_auth_exists(): void
    {
        $tenant = $this->tenant(Locale::French);

        TenantDomain::query()->create([
            'tenant_id' => $tenant->id,
            'domain' => 'acme.aegoryx.test',
            'type' => TenantDomainType::Primary,
            'status' => TenantDomainStatus::Verified,
        ]);

        $this
            ->get('http://acme.aegoryx.test/login')
            ->assertOk()
            ->assertSee('<html lang="fr">', false)
            ->assertSee('Connexion tenant');
    }

    private function tenant(Locale $locale): Tenant
    {
        return Tenant::query()->create([
            'name' => 'Example Tenant',
            'slug' => 'example-tenant',
            'schema_name' => 'tenant_example',
            'status' => TenantStatus::Active,
            'locale' => $locale,
            'deployment_type' => TenantDeploymentType::Saas,
            'billing_model' => TenantBillingModel::Subscription,
            'license_type' => TenantLicenseType::SaasSubscription,
        ]);
    }
}
