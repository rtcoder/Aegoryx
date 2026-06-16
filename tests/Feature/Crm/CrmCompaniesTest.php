<?php

namespace Tests\Feature\Crm;

use App\Models\Landlord\Feature;
use App\Models\Landlord\Tenant;
use App\Models\Landlord\TenantDomain;
use App\Models\Landlord\TenantFeature;
use App\Models\Tenant\ActivityEntry;
use App\Models\Tenant\CrmCompany;
use App\Models\Tenant\CrmContact;
use App\Models\Tenant\User;
use App\Modules\Audit\Enums\ActivityEntryAction;
use App\Modules\Entitlements\Enums\FeatureStatus;
use App\Modules\Entitlements\Enums\TenantFeatureSource;
use App\Modules\Tenancy\Enums\TenantBillingModel;
use App\Modules\Tenancy\Enums\TenantDeploymentType;
use App\Modules\Tenancy\Enums\TenantDomainStatus;
use App\Modules\Tenancy\Enums\TenantDomainType;
use App\Modules\Tenancy\Enums\TenantLicenseType;
use App\Modules\Tenancy\Enums\TenantStatus;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

final class CrmCompaniesTest extends TestCase
{
    private Tenant $tenant;

    private User $user;

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

        $this->tenant = $this->tenant();
        $this->domain($this->tenant);
        $this->feature('crm');
        $this->manualOverride($this->tenant, 'crm', true);

        $this->user = User::query()->create([
            'name' => 'Tenant User',
            'email' => 'tenant@example.test',
            'password' => 'secret-password',
        ]);
    }

    public function test_guest_cannot_create_company(): void
    {
        $this
            ->post('http://acme.aegoryx.test/panel/crm/companies', [
                'name' => 'Acme Corp',
            ])
            ->assertRedirect('http://acme.aegoryx.test/login');
    }

    public function test_user_can_create_company_and_link_contacts(): void
    {
        $this->actingAs($this->user, 'web');

        $contact = CrmContact::query()->create([
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
        ]);

        $this
            ->post('http://acme.aegoryx.test/panel/crm/companies', [
                'name' => 'Acme Corp',
                'website' => 'https://example.test',
                'email' => 'hello@example.test',
                'phone' => '+48 123 123 123',
                'contact_ids' => [$contact->id],
            ])
            ->assertRedirect('http://acme.aegoryx.test/panel/crm/companies');

        $company = CrmCompany::query()->firstOrFail();
        $activity = ActivityEntry::query()->where('action', ActivityEntryAction::CrmCompanyCreated)->firstOrFail();

        $this->assertSame('Acme Corp', $company->name);
        $this->assertTrue($company->contacts()->whereKey($contact->id)->exists());
        $this->assertSame('[redacted]', $activity->after_json['email']);
        $this->assertSame('[redacted]', $activity->after_json['phone']);
        $this->assertSame([$contact->id], $activity->after_json['contact_ids']);
    }

    public function test_user_can_update_and_delete_company_without_destroying_contacts(): void
    {
        $this->actingAs($this->user, 'web');

        $contact = CrmContact::query()->create([
            'first_name' => 'Ada',
        ]);

        $company = CrmCompany::query()->create([
            'name' => 'Acme Corp',
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ]);

        $this
            ->patch("http://acme.aegoryx.test/panel/crm/companies/{$company->id}", [
                'name' => 'Acme Labs',
                'website' => 'https://labs.example.test',
                'email' => 'labs@example.test',
                'phone' => '+1 555 000',
                'contact_ids' => [$contact->id],
            ])
            ->assertRedirect('http://acme.aegoryx.test/panel/crm/companies');

        $this->assertSame('Acme Labs', $company->refresh()->name);
        $this->assertTrue($company->contacts()->whereKey($contact->id)->exists());
        $this->assertSame(ActivityEntryAction::CrmCompanyUpdated, ActivityEntry::query()->latest('id')->firstOrFail()->action);

        $this
            ->delete("http://acme.aegoryx.test/panel/crm/companies/{$company->id}")
            ->assertRedirect('http://acme.aegoryx.test/panel/crm/companies');

        $this->assertSoftDeleted('crm_companies', ['id' => $company->id]);
        $this->assertDatabaseHas('crm_contacts', ['id' => $contact->id, 'deleted_at' => null]);
        $this->assertSame($this->user->id, $company->refresh()->deleted_by);
        $this->assertSame(ActivityEntryAction::CrmCompanyDeleted, ActivityEntry::query()->latest('id')->firstOrFail()->action);
    }

    public function test_companies_index_renders(): void
    {
        $this->actingAs($this->user, 'web');

        CrmCompany::query()->create([
            'name' => 'Acme Corp',
            'website' => 'https://example.test',
        ]);

        $this
            ->get('http://acme.aegoryx.test/panel/crm/companies')
            ->assertOk()
            ->assertSee('Firmy')
            ->assertSee('Acme Corp')
            ->assertSee('https://example.test');
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

    private function feature(string $key): Feature
    {
        return Feature::query()->create([
            'key' => $key,
            'name' => strtoupper($key),
            'status' => FeatureStatus::Active,
        ]);
    }

    private function manualOverride(Tenant $tenant, string $featureKey, bool $enabled): TenantFeature
    {
        $feature = Feature::query()->where('key', $featureKey)->firstOrFail();

        return TenantFeature::query()->create([
            'tenant_id' => $tenant->id,
            'feature_id' => $feature->id,
            'enabled' => $enabled,
            'source' => TenantFeatureSource::Manual,
            'reason' => 'Test entitlement.',
        ]);
    }
}
