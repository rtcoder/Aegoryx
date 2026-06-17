<?php

namespace Tests\Feature\Crm;

use App\Models\Landlord\Tenant;
use App\Models\Landlord\TenantDomain;
use App\Models\Landlord\TenantFeature;
use App\Models\Tenant\ActivityEntry;
use App\Models\Tenant\CrmContact;
use App\Models\Tenant\User;
use App\Modules\Audit\Enums\ActivityEntryAction;
use App\Modules\Entitlements\Enums\TenantFeatureSource;
use App\Modules\Tenancy\Enums\TenantBillingModel;
use App\Modules\Tenancy\Enums\TenantDeploymentType;
use App\Modules\Tenancy\Enums\TenantDomainStatus;
use App\Modules\Tenancy\Enums\TenantDomainType;
use App\Modules\Tenancy\Enums\TenantLicenseType;
use App\Modules\Tenancy\Enums\TenantStatus;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class CrmContactsTest extends TestCase
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

    public function test_guest_cannot_create_contact(): void
    {
        $this
            ->post('http://acme.aegoryx.test/panel/crm/contacts', [
                'first_name' => 'Ada',
            ])
            ->assertRedirect('http://acme.aegoryx.test/login');
    }

    public function test_user_can_create_contact_with_encrypted_sensitive_fields_and_redacted_activity(): void
    {
        $this->actingAs($this->user, 'web');

        $this
            ->post('http://acme.aegoryx.test/panel/crm/contacts', [
                'first_name' => 'Ada',
                'last_name' => 'Lovelace',
                'email' => 'ada@example.test',
                'phone' => '+48 123 123 123',
                'position' => 'Founder',
                'notes' => 'Important lead.',
            ])
            ->assertRedirect('http://acme.aegoryx.test/panel/crm');

        $contact = CrmContact::query()->firstOrFail();
        $raw = DB::table('crm_contacts')->where('id', $contact->id)->first();
        $activity = ActivityEntry::query()->where('action', ActivityEntryAction::CrmContactCreated)->firstOrFail();

        $this->assertSame('Ada', $contact->first_name);
        $this->assertSame('ada@example.test', $contact->email);
        $this->assertSame('+48 123 123 123', $contact->phone);
        $this->assertNotSame('ada@example.test', $raw->email_encrypted);
        $this->assertNotSame('+48 123 123 123', $raw->phone_encrypted);
        $this->assertSame(CrmContact::hashLookup('ada@example.test'), $raw->email_hash);
        $this->assertSame('[redacted]', $activity->after_json['email']);
        $this->assertSame('[redacted]', $activity->after_json['phone']);
    }

    public function test_user_can_update_and_delete_contact(): void
    {
        $this->actingAs($this->user, 'web');

        $contact = CrmContact::query()->create([
            'first_name' => 'Ada',
            'email' => 'ada@example.test',
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ]);

        $this
            ->patch("http://acme.aegoryx.test/panel/crm/contacts/{$contact->id}", [
                'first_name' => 'Grace',
                'last_name' => 'Hopper',
                'email' => 'grace@example.test',
                'phone' => '+1 555 000',
                'position' => 'Engineer',
            ])
            ->assertRedirect('http://acme.aegoryx.test/panel/crm');

        $this->assertSame('Grace', $contact->refresh()->first_name);
        $this->assertSame(ActivityEntryAction::CrmContactUpdated, ActivityEntry::query()->latest('id')->firstOrFail()->action);

        $this
            ->delete("http://acme.aegoryx.test/panel/crm/contacts/{$contact->id}")
            ->assertRedirect('http://acme.aegoryx.test/panel/crm');

        $this->assertSoftDeleted('crm_contacts', ['id' => $contact->id]);
        $this->assertSame($this->user->id, $contact->refresh()->deleted_by);
        $this->assertSame(ActivityEntryAction::CrmContactDeleted, ActivityEntry::query()->latest('id')->firstOrFail()->action);
    }

    public function test_contacts_index_renders(): void
    {
        $this->actingAs($this->user, 'web');

        CrmContact::query()->create([
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
            'email' => 'ada@example.test',
        ]);

        $this
            ->get('http://acme.aegoryx.test/panel/crm')
            ->assertOk()
            ->assertSee('Kontakty')
            ->assertSee('Ada Lovelace')
            ->assertSee('ada@example.test');
    }

    public function test_contacts_index_can_be_searched(): void
    {
        $this->actingAs($this->user, 'web');

        CrmContact::query()->create([
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
            'position' => 'Founder',
        ]);

        CrmContact::query()->create([
            'first_name' => 'Grace',
            'last_name' => 'Hopper',
            'position' => 'Engineer',
        ]);

        $this
            ->get('http://acme.aegoryx.test/panel/crm?search=Founder')
            ->assertOk()
            ->assertSee('Ada Lovelace')
            ->assertDontSee('Grace Hopper');
    }

    public function test_contacts_index_can_be_sorted(): void
    {
        $this->actingAs($this->user, 'web');

        CrmContact::query()->create([
            'first_name' => 'Grace',
            'last_name' => 'Hopper',
            'email' => 'grace@example.test',
            'position' => 'Engineer',
        ]);

        CrmContact::query()->create([
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
            'email' => 'ada@example.test',
            'position' => 'Founder',
        ]);

        $this
            ->get('http://acme.aegoryx.test/panel/crm?sort=position&direction=asc')
            ->assertOk()
            ->assertSeeInOrder(['Engineer', 'Founder']);
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
