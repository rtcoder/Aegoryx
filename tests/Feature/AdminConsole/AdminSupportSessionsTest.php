<?php

namespace Tests\Feature\AdminConsole;

use App\Livewire\Landlord\Support\Index;
use App\Models\Landlord\Identity;
use App\Models\Landlord\SupportSession;
use App\Models\Landlord\Tenant;
use App\Modules\AdminConsole\Enums\SupportSessionStatus;
use App\Modules\Identity\Enums\IdentityStatus;
use App\Modules\Tenancy\Enums\TenantBillingModel;
use App\Modules\Tenancy\Enums\TenantDeploymentType;
use App\Modules\Tenancy\Enums\TenantLicenseType;
use App\Modules\Tenancy\Enums\TenantStatus;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;
use Tests\TestCase;

final class AdminSupportSessionsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh', [
            '--database' => 'sqlite',
            '--path' => 'database/migrations/landlord',
        ]);
    }

    public function test_superadmin_can_start_support_session_with_reason(): void
    {
        $superadmin = $this->superadmin();
        $this->actingAs($superadmin, 'landlord');
        $tenant = $this->tenant();

        Livewire::test(Index::class)
            ->set('tenantId', $tenant->id)
            ->set('reason', 'Debugging reported billing access issue.')
            ->set('durationMinutes', 30)
            ->call('start')
            ->assertHasNoErrors();

        $supportSession = SupportSession::query()->firstOrFail();

        $this->assertSame($tenant->id, $supportSession->tenant_id);
        $this->assertSame($superadmin->id, $supportSession->actor_id);
        $this->assertSame(SupportSessionStatus::Active, $supportSession->status);
        $this->assertSame('Debugging reported billing access issue.', $supportSession->reason);
        $this->assertTrue($supportSession->expires_at->isFuture());

        $this->assertSame($supportSession->id, session('landlord_support_session_id'));
        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $superadmin->id,
            'subject_type' => SupportSession::class,
            'subject_id' => $supportSession->id,
            'action' => 'support_session_started',
        ]);
    }

    public function test_support_session_requires_reason(): void
    {
        $this->actingAs($this->superadmin(), 'landlord');

        Livewire::test(Index::class)
            ->set('tenantId', $this->tenant()->id)
            ->set('reason', '')
            ->set('durationMinutes', 30)
            ->call('start')
            ->assertHasErrors(['reason']);

        $this->assertSame(0, SupportSession::query()->count());
    }

    public function test_superadmin_can_end_support_session(): void
    {
        $superadmin = $this->superadmin();
        $this->actingAs($superadmin, 'landlord');
        $supportSession = $this->supportSession($this->tenant(), $superadmin);

        $this->withSession([
            'landlord_support_session_id' => $supportSession->id,
            'landlord_support_tenant_id' => $supportSession->tenant_id,
            'landlord_support_expires_at' => $supportSession->expires_at->toISOString(),
        ]);

        Livewire::test(Index::class)
            ->call('end')
            ->assertHasNoErrors();

        $supportSession->refresh();

        $this->assertSame(SupportSessionStatus::Ended, $supportSession->status);
        $this->assertNotNull($supportSession->ended_at);
        $this->assertNull(session('landlord_support_session_id'));
        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $superadmin->id,
            'subject_type' => SupportSession::class,
            'subject_id' => $supportSession->id,
            'action' => 'support_session_ended',
        ]);
    }

    public function test_expired_support_session_is_closed_and_removed_from_context(): void
    {
        $superadmin = $this->superadmin();
        $this->actingAs($superadmin, 'landlord');
        $supportSession = $this->supportSession($this->tenant(), $superadmin, [
            'expires_at' => now()->subMinute(),
        ]);

        $this->withSession([
            'landlord_support_session_id' => $supportSession->id,
            'landlord_support_tenant_id' => $supportSession->tenant_id,
            'landlord_support_expires_at' => $supportSession->expires_at->toISOString(),
        ]);

        Livewire::test(Index::class)
            ->assertHasNoErrors();

        $supportSession->refresh();

        $this->assertSame(SupportSessionStatus::Expired, $supportSession->status);
        $this->assertNull(session('landlord_support_session_id'));
        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $superadmin->id,
            'subject_type' => SupportSession::class,
            'subject_id' => $supportSession->id,
            'action' => 'support_session_expired',
        ]);
    }

    private function superadmin(): Identity
    {
        return Identity::query()->create([
            'email' => 'admin@example.test',
            'is_super_admin' => true,
            'status' => IdentityStatus::Active,
        ]);
    }

    private function tenant(): Tenant
    {
        return Tenant::query()->create([
            'name' => 'Example Tenant',
            'slug' => 'example-tenant',
            'schema_name' => 'tenant_example',
            'status' => TenantStatus::Active,
            'deployment_type' => TenantDeploymentType::Saas,
            'billing_model' => TenantBillingModel::Subscription,
            'license_type' => TenantLicenseType::SaasSubscription,
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function supportSession(Tenant $tenant, Identity $actor, array $attributes = []): SupportSession
    {
        return SupportSession::query()->create(array_merge([
            'tenant_id' => $tenant->id,
            'actor_id' => $actor->id,
            'status' => SupportSessionStatus::Active,
            'reason' => 'Investigating support request.',
            'started_at' => now(),
            'expires_at' => now()->addMinutes(30),
        ], $attributes));
    }
}
