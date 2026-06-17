<?php

namespace Tests\Feature\Crm;

use App\Models\Landlord\Tenant;
use App\Models\Landlord\TenantDomain;
use App\Models\Landlord\TenantFeature;
use App\Models\Tenant\ActivityEntry;
use App\Models\Tenant\CrmCompany;
use App\Models\Tenant\CrmNote;
use App\Models\Tenant\User;
use App\Modules\Audit\Enums\ActivityEntryAction;
use App\Modules\Crm\Enums\CrmSubjectType;
use App\Modules\Entitlements\Enums\TenantFeatureSource;
use App\Modules\Tenancy\Enums\TenantBillingModel;
use App\Modules\Tenancy\Enums\TenantDeploymentType;
use App\Modules\Tenancy\Enums\TenantDomainStatus;
use App\Modules\Tenancy\Enums\TenantDomainType;
use App\Modules\Tenancy\Enums\TenantLicenseType;
use App\Modules\Tenancy\Enums\TenantStatus;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

final class CrmNotesTest extends TestCase
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

    public function test_guest_cannot_create_note(): void
    {
        $company = CrmCompany::query()->create(['name' => 'Acme Corp']);

        $this
            ->post('http://acme.aegoryx.test/panel/crm/notes', [
                'subject' => 'company:'.$company->id,
                'body' => 'Call summary.',
            ])
            ->assertRedirect('http://acme.aegoryx.test/login');
    }

    public function test_user_can_create_note_for_company(): void
    {
        $this->actingAs($this->user, 'web');
        $company = CrmCompany::query()->create(['name' => 'Acme Corp']);

        $this
            ->post('http://acme.aegoryx.test/panel/crm/notes', [
                'subject' => 'company:'.$company->id,
                'body' => 'Customer wants a follow-up next week.',
                'is_sensitive' => '1',
            ])
            ->assertRedirect('http://acme.aegoryx.test/panel/crm/notes');

        $note = CrmNote::query()->firstOrFail();
        $activity = ActivityEntry::query()->where('action', ActivityEntryAction::CrmNoteCreated)->firstOrFail();

        $this->assertSame(CrmSubjectType::Company, $note->subject_type);
        $this->assertSame($company->id, $note->subject_id);
        $this->assertSame('Customer wants a follow-up next week.', $note->body);
        $this->assertTrue($note->is_sensitive);
        $this->assertSame($this->user->id, $note->created_by);
        $this->assertSame(CrmSubjectType::Company->value, $activity->after_json['subject_type']);
        $this->assertSame('[redacted]', $activity->after_json['body']);
    }

    public function test_user_can_update_and_delete_note(): void
    {
        $this->actingAs($this->user, 'web');
        $company = CrmCompany::query()->create(['name' => 'Acme Corp']);
        $note = CrmNote::query()->create([
            'subject_type' => CrmSubjectType::Company,
            'subject_id' => $company->id,
            'body' => 'Initial note.',
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ]);

        $this
            ->patch("http://acme.aegoryx.test/panel/crm/notes/{$note->id}", [
                'subject' => 'company:'.$company->id,
                'body' => 'Updated note.',
            ])
            ->assertRedirect('http://acme.aegoryx.test/panel/crm/notes');

        $this->assertSame('Updated note.', $note->refresh()->body);
        $this->assertSame(ActivityEntryAction::CrmNoteUpdated, ActivityEntry::query()->latest('id')->firstOrFail()->action);

        $this
            ->delete("http://acme.aegoryx.test/panel/crm/notes/{$note->id}")
            ->assertRedirect('http://acme.aegoryx.test/panel/crm/notes');

        $this->assertSoftDeleted('crm_notes', ['id' => $note->id]);
        $this->assertSame($this->user->id, $note->refresh()->deleted_by);
        $this->assertSame(ActivityEntryAction::CrmNoteDeleted, ActivityEntry::query()->latest('id')->firstOrFail()->action);
    }

    public function test_notes_index_renders(): void
    {
        $this->actingAs($this->user, 'web');
        $company = CrmCompany::query()->create(['name' => 'Acme Corp']);

        CrmNote::query()->create([
            'subject_type' => CrmSubjectType::Company,
            'subject_id' => $company->id,
            'body' => 'Important decision.',
        ]);

        $this
            ->get('http://acme.aegoryx.test/panel/crm/notes')
            ->assertOk()
            ->assertSee('Notatki')
            ->assertSee('Important decision.')
            ->assertSee('Acme Corp');
    }

    public function test_notes_index_can_be_searched(): void
    {
        $this->actingAs($this->user, 'web');
        $company = CrmCompany::query()->create(['name' => 'Acme Corp']);

        CrmNote::query()->create([
            'subject_type' => CrmSubjectType::Company,
            'subject_id' => $company->id,
            'body' => 'Important decision.',
        ]);
        CrmNote::query()->create([
            'subject_type' => CrmSubjectType::Company,
            'subject_id' => $company->id,
            'body' => 'Casual note.',
        ]);

        $this
            ->get('http://acme.aegoryx.test/panel/crm/notes?search=Casual')
            ->assertOk()
            ->assertSee('Casual note.')
            ->assertDontSee('Important decision.');
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
