<?php

namespace Tests\Feature\Crm;

use App\Models\Landlord\Tenant;
use App\Models\Landlord\TenantDomain;
use App\Models\Landlord\TenantFeature;
use App\Models\Tenant\ActivityEntry;
use App\Models\Tenant\CrmCompany;
use App\Models\Tenant\CrmTask;
use App\Models\Tenant\User;
use App\Modules\Audit\Enums\ActivityEntryAction;
use App\Modules\Crm\Enums\CrmSubjectType;
use App\Modules\Crm\Enums\CrmTaskStatus;
use App\Modules\Entitlements\Enums\TenantFeatureSource;
use App\Modules\Tenancy\Enums\TenantBillingModel;
use App\Modules\Tenancy\Enums\TenantDeploymentType;
use App\Modules\Tenancy\Enums\TenantDomainStatus;
use App\Modules\Tenancy\Enums\TenantDomainType;
use App\Modules\Tenancy\Enums\TenantLicenseType;
use App\Modules\Tenancy\Enums\TenantStatus;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

final class CrmTasksTest extends TestCase
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

    public function test_guest_cannot_create_task(): void
    {
        $company = CrmCompany::query()->create(['name' => 'Acme Corp']);

        $this
            ->post('http://acme.aegoryx.test/panel/crm/tasks', [
                'subject' => 'company:'.$company->id,
                'title' => 'Call buyer',
                'status' => CrmTaskStatus::Pending->value,
            ])
            ->assertRedirect('http://acme.aegoryx.test/login');
    }

    public function test_user_can_create_task_for_company(): void
    {
        $this->actingAs($this->user, 'web');
        $company = CrmCompany::query()->create(['name' => 'Acme Corp']);

        $this
            ->post('http://acme.aegoryx.test/panel/crm/tasks', [
                'subject' => 'company:'.$company->id,
                'title' => 'Call buyer',
                'description' => 'Confirm budget.',
                'status' => CrmTaskStatus::Pending->value,
                'due_date' => '2026-07-10',
                'assigned_to' => $this->user->id,
            ])
            ->assertRedirect('http://acme.aegoryx.test/panel/crm/tasks');

        $task = CrmTask::query()->firstOrFail();
        $activity = ActivityEntry::query()->where('action', ActivityEntryAction::CrmTaskCreated)->firstOrFail();

        $this->assertSame(CrmSubjectType::Company, $task->subject_type);
        $this->assertSame($company->id, $task->subject_id);
        $this->assertSame(CrmTaskStatus::Pending, $task->status);
        $this->assertSame('2026-07-10', $task->due_date?->toDateString());
        $this->assertSame($this->user->id, $activity->after_json['assigned_to']);
    }

    public function test_user_can_complete_and_delete_task(): void
    {
        $this->actingAs($this->user, 'web');
        $company = CrmCompany::query()->create(['name' => 'Acme Corp']);
        $task = CrmTask::query()->create([
            'subject_type' => CrmSubjectType::Company,
            'subject_id' => $company->id,
            'title' => 'Call buyer',
            'status' => CrmTaskStatus::Pending,
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ]);

        $this
            ->patch("http://acme.aegoryx.test/panel/crm/tasks/{$task->id}", [
                'subject' => 'company:'.$company->id,
                'title' => 'Call buyer',
                'status' => CrmTaskStatus::Completed->value,
                'due_date' => '2026-07-11',
                'assigned_to' => $this->user->id,
            ])
            ->assertRedirect('http://acme.aegoryx.test/panel/crm/tasks');

        $this->assertSame(CrmTaskStatus::Completed, $task->refresh()->status);
        $this->assertNotNull($task->completed_at);
        $this->assertSame(ActivityEntryAction::CrmTaskUpdated, ActivityEntry::query()->latest('id')->firstOrFail()->action);

        $this
            ->delete("http://acme.aegoryx.test/panel/crm/tasks/{$task->id}")
            ->assertRedirect('http://acme.aegoryx.test/panel/crm/tasks');

        $this->assertSoftDeleted('crm_tasks', ['id' => $task->id]);
        $this->assertSame($this->user->id, $task->refresh()->deleted_by);
        $this->assertSame(ActivityEntryAction::CrmTaskDeleted, ActivityEntry::query()->latest('id')->firstOrFail()->action);
    }

    public function test_tasks_index_renders(): void
    {
        $this->actingAs($this->user, 'web');
        $company = CrmCompany::query()->create(['name' => 'Acme Corp']);

        CrmTask::query()->create([
            'subject_type' => CrmSubjectType::Company,
            'subject_id' => $company->id,
            'title' => 'Call buyer',
            'status' => CrmTaskStatus::Pending,
        ]);

        $this
            ->get('http://acme.aegoryx.test/panel/crm/tasks')
            ->assertOk()
            ->assertSee('Zadania')
            ->assertSee('Call buyer')
            ->assertSee('Acme Corp');
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
