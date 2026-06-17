<?php

namespace Tests\Feature\TenantPanel;

use App\Livewire\Tenant\Users\Index;
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
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

final class TenantUsersTest extends TestCase
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

    public function test_owner_can_render_users_page_and_update_role(): void
    {
        $tenant = $this->tenant();
        $this->domain($tenant);
        $owner = $this->user(TenantUserRole::Owner, 'owner@example.test');
        $member = $this->user(TenantUserRole::Member, 'member@example.test');
        $this->actingAs($owner, 'web');

        $this
            ->get('http://acme.aegoryx.test/panel/users')
            ->assertOk()
            ->assertSee(__('tenant_panel.users.title'))
            ->assertSee($member->email);

        Livewire::test(Index::class)
            ->set("roles.{$member->id}", TenantUserRole::Viewer->value)
            ->call('updateRole', $member->id)
            ->assertHasNoErrors();

        $this->assertSame(TenantUserRole::Viewer, $member->refresh()->role);
    }

    public function test_owner_can_create_user_with_generated_password(): void
    {
        $owner = $this->user(TenantUserRole::Owner, 'owner@example.test');
        $this->actingAs($owner, 'web');

        $component = Livewire::test(Index::class)
            ->set('name', 'New Member')
            ->set('email', 'new-member@example.test')
            ->set('newRole', TenantUserRole::Member->value)
            ->call('createUser')
            ->assertHasNoErrors();

        $created = User::query()->where('email', 'new-member@example.test')->firstOrFail();
        $generatedPassword = $component->get('generatedPassword');

        $this->assertSame(TenantUserRole::Member, $created->role);
        $this->assertIsString($generatedPassword);
        $this->assertTrue(Hash::check($generatedPassword, $created->password));
        $this->assertSame($owner->id, $created->created_by);
    }

    public function test_member_cannot_create_user(): void
    {
        $member = $this->user(TenantUserRole::Member, 'member@example.test');
        $this->actingAs($member, 'web');

        Livewire::test(Index::class)
            ->set('name', 'Blocked User')
            ->set('email', 'blocked@example.test')
            ->set('newRole', TenantUserRole::Viewer->value)
            ->set('password', 'very-secret-password')
            ->call('createUser')
            ->assertForbidden();

        $this->assertFalse(User::query()->where('email', 'blocked@example.test')->exists());
    }

    public function test_member_cannot_update_roles(): void
    {
        $member = $this->user(TenantUserRole::Member, 'member@example.test');
        $viewer = $this->user(TenantUserRole::Viewer, 'viewer@example.test');
        $this->actingAs($member, 'web');

        Livewire::test(Index::class)
            ->set("roles.{$viewer->id}", TenantUserRole::Admin->value)
            ->call('updateRole', $viewer->id)
            ->assertForbidden();

        $this->assertSame(TenantUserRole::Viewer, $viewer->refresh()->role);
    }

    public function test_last_owner_cannot_be_demoted(): void
    {
        $owner = $this->user(TenantUserRole::Owner, 'owner@example.test');
        $this->actingAs($owner, 'web');

        Livewire::test(Index::class)
            ->set("roles.{$owner->id}", TenantUserRole::Admin->value)
            ->call('updateRole', $owner->id)
            ->assertHasErrors(['role']);

        $this->assertSame(TenantUserRole::Owner, $owner->refresh()->role);
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

    private function user(TenantUserRole $role, string $email): User
    {
        return User::query()->create([
            'name' => ucfirst($role->value),
            'email' => $email,
            'password' => 'secret-password',
            'role' => $role,
        ]);
    }
}
