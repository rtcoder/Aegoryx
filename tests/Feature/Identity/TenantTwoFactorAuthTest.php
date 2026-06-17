<?php

namespace Tests\Feature\Identity;

use App\Models\Tenant\ActivityEntry;
use App\Models\Tenant\User;
use App\Modules\Audit\Enums\ActivityEntryAction;
use App\Modules\Identity\Actions\DisableTenantTwoFactorAuthAction;
use App\Modules\Identity\Actions\EnableTenantTwoFactorAuthAction;
use App\Modules\Identity\Enums\TenantUserRole;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class TenantTwoFactorAuthTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh', [
            '--database' => 'sqlite',
            '--path' => 'database/migrations/tenant',
        ]);
    }

    public function test_tenant_user_can_enable_own_two_factor_with_activity(): void
    {
        $user = $this->user(TenantUserRole::Member);

        $updated = app(EnableTenantTwoFactorAuthAction::class)->handle(
            user: $user,
            secret: 'tenant-secret-do-not-log',
            recoveryCodes: ['first-code', 'second-code'],
            actor: $user,
            ip: '127.0.0.1',
            userAgent: 'Test Agent',
        );

        $raw = DB::table('users')->where('id', $user->id)->first();
        $activity = ActivityEntry::query()->where('action', ActivityEntryAction::TenantTwoFactorEnabled)->firstOrFail();

        $this->assertTrue($updated->hasTwoFactorEnabled());
        $this->assertSame('tenant-secret-do-not-log', $updated->two_factor_secret);
        $this->assertNotSame('tenant-secret-do-not-log', $raw->two_factor_secret);
        $this->assertTrue(Hash::check('first-code', $updated->two_factor_recovery_codes[0]));
        $this->assertSame($user->id, $activity->actor_id);
        $this->assertSame($user->id, $activity->subject_id);
        $this->assertSame(2, $activity->after_json['recovery_code_count']);
        $this->assertSame('127.0.0.1', $activity->ip);
        $this->assertFalse(str_contains(json_encode($activity->toArray(), JSON_THROW_ON_ERROR), 'tenant-secret-do-not-log'));
        $this->assertFalse(str_contains(json_encode($activity->toArray(), JSON_THROW_ON_ERROR), 'first-code'));
    }

    public function test_owner_can_disable_two_factor_for_another_tenant_user(): void
    {
        $owner = $this->user(TenantUserRole::Owner, 'owner@example.test');
        $member = $this->user(TenantUserRole::Member, 'member@example.test');

        app(EnableTenantTwoFactorAuthAction::class)->handle($member, 'tenant-secret-do-not-log', ['first-code'], $owner, null, null);

        $updated = app(DisableTenantTwoFactorAuthAction::class)->handle(
            user: $member->refresh(),
            actor: $owner,
            ip: null,
            userAgent: null,
        );

        $activity = ActivityEntry::query()->where('action', ActivityEntryAction::TenantTwoFactorDisabled)->firstOrFail();

        $this->assertFalse($updated->hasTwoFactorEnabled());
        $this->assertNull($updated->two_factor_secret);
        $this->assertNull($updated->two_factor_recovery_codes);
        $this->assertTrue($activity->before_json['two_factor_enabled']);
        $this->assertFalse($activity->after_json['two_factor_enabled']);
    }

    public function test_member_cannot_change_two_factor_for_another_user(): void
    {
        $member = $this->user(TenantUserRole::Member, 'member@example.test');
        $viewer = $this->user(TenantUserRole::Viewer, 'viewer@example.test');

        $this->expectException(AuthorizationException::class);

        app(EnableTenantTwoFactorAuthAction::class)->handle($viewer, 'tenant-secret-do-not-log', ['first-code'], $member, null, null);
    }

    private function user(TenantUserRole $role, string $email = 'user@example.test'): User
    {
        return User::query()->create([
            'name' => ucfirst($role->value),
            'email' => $email,
            'password' => 'secret-password',
            'role' => $role,
        ]);
    }
}
