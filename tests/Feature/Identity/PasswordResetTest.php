<?php

namespace Tests\Feature\Identity;

use App\Livewire\Admin\Auth\RequestPasswordResetForm as SuperadminRequestPasswordResetForm;
use App\Livewire\Admin\Auth\ResetPasswordForm as SuperadminResetPasswordForm;
use App\Livewire\Tenant\Auth\RequestPasswordResetForm as TenantRequestPasswordResetForm;
use App\Livewire\Tenant\Auth\ResetPasswordForm as TenantResetPasswordForm;
use App\Models\System\Identity;
use App\Models\System\Tenant;
use App\Models\System\TenantDomain;
use App\Models\Tenant\User;
use App\Modules\Identity\Enums\IdentityStatus;
use App\Modules\Identity\Enums\TenantUserRole;
use App\Modules\Identity\Notifications\PasswordResetLinkNotification;
use App\Modules\Tenancy\Enums\TenantBillingModel;
use App\Modules\Tenancy\Enums\TenantDeploymentType;
use App\Modules\Tenancy\Enums\TenantDomainStatus;
use App\Modules\Tenancy\Enums\TenantDomainType;
use App\Modules\Tenancy\Enums\TenantLicenseType;
use App\Modules\Tenancy\Enums\TenantStatus;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

final class PasswordResetTest extends TestCase
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

    public function test_tenant_user_can_reset_password_with_token(): void
    {
        Notification::fake();

        $user = User::query()->create([
            'name' => 'Member',
            'email' => 'member@example.test',
            'password' => 'old-secret-password',
            'role' => TenantUserRole::Member,
        ]);

        $request = Livewire::test(TenantRequestPasswordResetForm::class)
            ->set('email', 'member@example.test')
            ->call('requestReset')
            ->assertHasNoErrors();

        $token = $request->get('token');
        $this->assertIsString($token);
        $this->assertDatabaseHas('password_reset_tokens', ['email' => 'member@example.test']);
        Notification::assertSentTo(
            $user,
            PasswordResetLinkNotification::class,
            fn (PasswordResetLinkNotification $notification): bool => str_contains($notification->toArray($user)['url'], $token),
        );

        Livewire::test(TenantResetPasswordForm::class, ['token' => $token])
            ->set('email', 'member@example.test')
            ->set('password', 'new-secret-password')
            ->set('password_confirmation', 'new-secret-password')
            ->call('resetPassword')
            ->assertHasNoErrors();

        $this->assertTrue(Hash::check('new-secret-password', $user->refresh()->password));
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => 'member@example.test']);
    }

    public function test_tenant_password_reset_request_is_generic_for_unknown_email(): void
    {
        Notification::fake();

        Livewire::test(TenantRequestPasswordResetForm::class)
            ->set('email', 'missing@example.test')
            ->call('requestReset')
            ->assertHasNoErrors()
            ->assertSet('token', null);

        $this->assertSame(0, DB::table('password_reset_tokens')->count());
        Notification::assertNothingSent();
    }

    public function test_admin_superadmin_can_reset_password_with_token(): void
    {
        Notification::fake();

        $identity = Identity::query()->create([
            'email' => 'root@example.test',
            'password' => 'old-secret-password',
            'is_super_admin' => true,
            'status' => IdentityStatus::Active,
        ]);

        $request = Livewire::test(SuperadminRequestPasswordResetForm::class)
            ->set('email', 'root@example.test')
            ->call('requestReset')
            ->assertHasNoErrors();

        $token = $request->get('token');
        $this->assertIsString($token);
        $this->assertDatabaseHas('password_reset_tokens', ['email' => 'root@example.test']);
        Notification::assertSentTo(
            $identity,
            PasswordResetLinkNotification::class,
            fn (PasswordResetLinkNotification $notification): bool => str_contains($notification->toArray($identity)['url'], $token),
        );

        Livewire::test(SuperadminResetPasswordForm::class, ['token' => $token])
            ->set('email', 'root@example.test')
            ->set('password', 'new-secret-password')
            ->set('password_confirmation', 'new-secret-password')
            ->call('resetPassword')
            ->assertHasNoErrors();

        $this->assertTrue(Hash::check('new-secret-password', $identity->refresh()->password));
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => 'root@example.test']);
    }

    public function test_password_reset_routes_render_in_their_domains(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Acme Tenant',
            'slug' => 'acme',
            'schema_name' => 'tenant_acme',
            'status' => TenantStatus::Active,
            'deployment_type' => TenantDeploymentType::Saas,
            'billing_model' => TenantBillingModel::Subscription,
            'license_type' => TenantLicenseType::SaasSubscription,
        ]);
        TenantDomain::query()->create([
            'tenant_id' => $tenant->id,
            'domain' => 'acme.aegoryx.test',
            'type' => TenantDomainType::Primary,
            'status' => TenantDomainStatus::Verified,
        ]);

        $this->get('http://acme.aegoryx.test/forgot-password')
            ->assertOk()
            ->assertSee(__('common.password_reset_request_heading'));

        $this->get('http://admin.aegoryx.test/forgot-password')
            ->assertOk()
            ->assertSee(__('common.password_reset_request_heading'));
    }
}
