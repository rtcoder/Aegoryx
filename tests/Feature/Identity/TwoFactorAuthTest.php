<?php

namespace Tests\Feature\Identity;

use App\Models\Landlord\AuditLog;
use App\Models\Landlord\Identity;
use App\Modules\Audit\Enums\AuditLogAction;
use App\Modules\Identity\Actions\DisableTwoFactorAuthAction;
use App\Modules\Identity\Actions\EnableTwoFactorAuthAction;
use App\Modules\Identity\Enums\IdentityStatus;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class TwoFactorAuthTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh', [
            '--database' => 'sqlite',
            '--path' => 'database/migrations/landlord',
        ]);
    }

    public function test_two_factor_can_be_enabled_with_encrypted_secret_hashed_recovery_codes_and_audit(): void
    {
        $identity = $this->identity();

        $updated = app(EnableTwoFactorAuthAction::class)->handle(
            identity: $identity,
            secret: 'totp-secret-do-not-log',
            recoveryCodes: ['first-code', 'second-code'],
            actor: $identity,
            ip: '127.0.0.1',
            userAgent: 'Test Agent',
        );

        $raw = DB::table('identities')->where('id', $identity->id)->first();
        $auditLog = AuditLog::query()->where('action', AuditLogAction::TwoFactorEnabled)->firstOrFail();

        $this->assertTrue($updated->hasTwoFactorEnabled());
        $this->assertSame('totp-secret-do-not-log', $updated->two_factor_secret);
        $this->assertNotSame('totp-secret-do-not-log', $raw->two_factor_secret);
        $this->assertTrue(Hash::check('first-code', $updated->two_factor_recovery_codes[0]));
        $this->assertFalse(str_contains(json_encode($auditLog->toArray(), JSON_THROW_ON_ERROR), 'totp-secret-do-not-log'));
        $this->assertFalse(str_contains(json_encode($auditLog->toArray(), JSON_THROW_ON_ERROR), 'first-code'));
        $this->assertSame(2, $auditLog->after_json['recovery_code_count']);
    }

    public function test_two_factor_can_be_disabled_and_audited(): void
    {
        $identity = $this->identity();
        app(EnableTwoFactorAuthAction::class)->handle(
            identity: $identity,
            secret: 'totp-secret-do-not-log',
            recoveryCodes: ['first-code'],
            actor: $identity,
            ip: null,
            userAgent: null,
        );

        $updated = app(DisableTwoFactorAuthAction::class)->handle(
            identity: $identity->refresh(),
            actor: $identity,
            ip: null,
            userAgent: null,
        );

        $auditLog = AuditLog::query()->where('action', AuditLogAction::TwoFactorDisabled)->firstOrFail();

        $this->assertFalse($updated->hasTwoFactorEnabled());
        $this->assertNull($updated->two_factor_secret);
        $this->assertNull($updated->two_factor_recovery_codes);
        $this->assertNull($updated->two_factor_confirmed_at);
        $this->assertTrue($auditLog->before_json['two_factor_enabled']);
        $this->assertFalse($auditLog->after_json['two_factor_enabled']);
    }

    private function identity(): Identity
    {
        return Identity::query()->create([
            'email' => 'admin@example.test',
            'is_super_admin' => true,
            'status' => IdentityStatus::Active,
        ]);
    }
}
