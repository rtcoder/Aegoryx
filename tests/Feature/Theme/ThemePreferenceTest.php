<?php

namespace Tests\Feature\Theme;

use App\Models\System\Identity;
use App\Models\System\Tenant;
use App\Models\System\TenantDomain;
use App\Models\Tenant\User;
use App\Modules\Identity\Enums\IdentityStatus;
use App\Modules\Identity\Enums\TenantUserRole;
use App\Modules\Tenancy\Enums\TenantBillingModel;
use App\Modules\Tenancy\Enums\TenantDeploymentType;
use App\Modules\Tenancy\Enums\TenantDomainStatus;
use App\Modules\Tenancy\Enums\TenantDomainType;
use App\Modules\Tenancy\Enums\TenantLicenseType;
use App\Modules\Tenancy\Enums\TenantStatus;
use App\Support\Localization\Locale;
use App\Support\Theme\ThemePreference;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

final class ThemePreferenceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh', [
            '--database' => 'sqlite',
            '--path' => 'database/migrations/system',
        ]);

        Artisan::call('migrate', [
            '--database' => 'sqlite',
            '--path' => 'database/migrations/tenant',
        ]);
    }

    public function test_system_identity_theme_defaults_to_light_and_casts_to_enum(): void
    {
        $identity = Identity::query()->create([
            'email' => 'admin@example.test',
            'is_super_admin' => true,
            'status' => IdentityStatus::Active,
            'locale' => Locale::Polish,
        ]);

        $this->assertSame(ThemePreference::Light, $identity->refresh()->theme);

        $identity->forceFill(['theme' => ThemePreference::Dark])->save();

        $this->assertSame('dark', $identity->refresh()->getRawOriginal('theme'));
        $this->assertSame(ThemePreference::Dark, $identity->theme);
    }

    public function test_tenant_user_theme_defaults_to_light_and_casts_to_enum(): void
    {
        $this->domain($this->tenant());

        $user = User::query()->create([
            'name' => 'Member',
            'email' => 'member@example.test',
            'password' => 'secret-password',
            'role' => TenantUserRole::Member,
            'locale' => Locale::Polish,
        ]);

        $this->assertSame(ThemePreference::Light, $user->refresh()->theme);

        $user->forceFill(['theme' => ThemePreference::Dark])->save();

        $this->assertSame('dark', $user->refresh()->getRawOriginal('theme'));
        $this->assertSame(ThemePreference::Dark, $user->theme);
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
