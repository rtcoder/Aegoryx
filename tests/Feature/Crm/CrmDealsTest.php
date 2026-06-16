<?php

namespace Tests\Feature\Crm;

use App\Models\Landlord\Tenant;
use App\Models\Landlord\TenantDomain;
use App\Models\Landlord\TenantFeature;
use App\Models\Tenant\ActivityEntry;
use App\Models\Tenant\CrmCompany;
use App\Models\Tenant\CrmContact;
use App\Models\Tenant\CrmDeal;
use App\Models\Tenant\User;
use App\Modules\Audit\Enums\ActivityEntryAction;
use App\Modules\Crm\Enums\CrmDealStatus;
use App\Modules\Entitlements\Enums\TenantFeatureSource;
use App\Modules\Tenancy\Enums\TenantBillingModel;
use App\Modules\Tenancy\Enums\TenantDeploymentType;
use App\Modules\Tenancy\Enums\TenantDomainStatus;
use App\Modules\Tenancy\Enums\TenantDomainType;
use App\Modules\Tenancy\Enums\TenantLicenseType;
use App\Modules\Tenancy\Enums\TenantStatus;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

final class CrmDealsTest extends TestCase
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
        $this->manualOverride($this->tenant, 'crm', true);

        $this->user = User::query()->create([
            'name' => 'Tenant User',
            'email' => 'tenant@example.test',
            'password' => 'secret-password',
        ]);
    }

    public function test_guest_cannot_create_deal(): void
    {
        $this
            ->post('http://acme.aegoryx.test/panel/crm/deals', [
                'title' => 'Enterprise license',
                'status' => CrmDealStatus::Open->value,
            ])
            ->assertRedirect('http://acme.aegoryx.test/login');
    }

    public function test_user_can_create_deal_with_company_and_contact(): void
    {
        $this->actingAs($this->user, 'web');

        $company = CrmCompany::query()->create(['name' => 'Acme Corp']);
        $contact = CrmContact::query()->create(['first_name' => 'Ada']);

        $this
            ->post('http://acme.aegoryx.test/panel/crm/deals', [
                'title' => 'Enterprise license',
                'company_id' => $company->id,
                'contact_id' => $contact->id,
                'status' => CrmDealStatus::Open->value,
                'value_amount' => '1200.50',
                'currency' => 'EUR',
                'expected_close_date' => '2026-07-01',
            ])
            ->assertRedirect('http://acme.aegoryx.test/panel/crm/deals');

        $deal = CrmDeal::query()->firstOrFail();
        $activity = ActivityEntry::query()->where('action', ActivityEntryAction::CrmDealCreated)->firstOrFail();

        $this->assertSame('Enterprise license', $deal->title);
        $this->assertSame($company->id, $deal->company_id);
        $this->assertSame($contact->id, $deal->contact_id);
        $this->assertSame(CrmDealStatus::Open, $deal->status);
        $this->assertSame('EUR', $deal->currency);
        $this->assertSame(CrmDealStatus::Open->value, $activity->after_json['status']);
    }

    public function test_user_can_update_status_and_delete_deal(): void
    {
        $this->actingAs($this->user, 'web');

        $deal = CrmDeal::query()->create([
            'title' => 'Enterprise license',
            'status' => CrmDealStatus::Open,
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ]);

        $this
            ->patch("http://acme.aegoryx.test/panel/crm/deals/{$deal->id}", [
                'title' => 'Enterprise license',
                'status' => CrmDealStatus::Won->value,
                'value_amount' => '1400',
                'currency' => 'EUR',
                'expected_close_date' => '2026-07-02',
            ])
            ->assertRedirect('http://acme.aegoryx.test/panel/crm/deals');

        $this->assertSame(CrmDealStatus::Won, $deal->refresh()->status);
        $this->assertSame(ActivityEntryAction::CrmDealUpdated, ActivityEntry::query()->latest('id')->firstOrFail()->action);

        $this
            ->delete("http://acme.aegoryx.test/panel/crm/deals/{$deal->id}")
            ->assertRedirect('http://acme.aegoryx.test/panel/crm/deals');

        $this->assertSoftDeleted('crm_deals', ['id' => $deal->id]);
        $this->assertSame($this->user->id, $deal->refresh()->deleted_by);
        $this->assertSame(ActivityEntryAction::CrmDealDeleted, ActivityEntry::query()->latest('id')->firstOrFail()->action);
    }

    public function test_deals_index_renders(): void
    {
        $this->actingAs($this->user, 'web');

        CrmDeal::query()->create([
            'title' => 'Enterprise license',
            'status' => CrmDealStatus::Open,
        ]);

        $this
            ->get('http://acme.aegoryx.test/panel/crm/deals')
            ->assertOk()
            ->assertSee('Deale')
            ->assertSee('Enterprise license')
            ->assertSee('Otwarty');
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

    private function manualOverride(Tenant $tenant, string $featureKey, bool $enabled): TenantFeature
    {
        return TenantFeature::query()->create([
            'tenant_id' => $tenant->id,
            'feature' => $featureKey,
            'enabled' => $enabled,
            'source' => TenantFeatureSource::Manual,
            'reason' => 'Test entitlement.',
        ]);
    }
}
