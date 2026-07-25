<?php

namespace Tests\Feature\Identity;

use App\Livewire\Admin\Auth\LoginForm;
use App\Livewire\Admin\Auth\TwoFactorChallengeForm;
use App\Livewire\Admin\Security\TwoFactorSettings;
use App\Models\System\Identity;
use App\Modules\Identity\Actions\EnableTwoFactorAuthAction;
use App\Modules\Identity\Enums\IdentityStatus;
use App\Modules\Identity\Support\TwoFactorAuthenticator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\TestCase;

final class SuperadminTwoFactorLoginTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh', [
            '--database' => 'sqlite',
            '--path' => 'database/migrations/system',
        ]);
    }

    public function test_admin_login_requires_two_factor_challenge_when_enabled(): void
    {
        $identity = $this->twoFactorIdentity();

        Livewire::test(LoginForm::class)
            ->set('email', $identity->email)
            ->set('password', 'secret-password')
            ->call('login')
            ->assertRedirect(route('admin.two-factor.challenge'));

        $this->assertFalse(Auth::guard('admin')->check());
        $this->assertSame($identity->id, session('admin_login_2fa_identity_id'));
    }

    public function test_admin_can_complete_two_factor_challenge_with_totp_code(): void
    {
        $identity = $this->twoFactorIdentity();
        $this->withSession(['admin_login_2fa_identity_id' => $identity->id]);

        Livewire::test(TwoFactorChallengeForm::class)
            ->set('code', app(TwoFactorAuthenticator::class)->currentCode($identity->two_factor_secret))
            ->call('verify')
            ->assertRedirect(route('admin.dashboard'));

        $this->assertTrue(Auth::guard('admin')->check());
        $this->assertNull(session('admin_login_2fa_identity_id'));
    }

    public function test_admin_can_complete_two_factor_challenge_with_recovery_code_once(): void
    {
        $identity = $this->twoFactorIdentity();
        $this->withSession(['admin_login_2fa_identity_id' => $identity->id]);

        Livewire::test(TwoFactorChallengeForm::class)
            ->set('code', 'RECOVERY-ONE')
            ->call('verify')
            ->assertRedirect(route('admin.dashboard'));

        $this->assertTrue(Auth::guard('admin')->check());
        $this->assertCount(1, $identity->refresh()->two_factor_recovery_codes);
    }

    public function test_security_screen_can_enable_and_disable_two_factor(): void
    {
        $identity = $this->identity();
        $this->actingAs($identity, 'admin');

        $component = Livewire::test(TwoFactorSettings::class)
            ->call('generate');

        $secret = $component->get('pendingSecret');

        $component
            ->set('code', app(TwoFactorAuthenticator::class)->currentCode($secret))
            ->call('enable')
            ->assertHasNoErrors();

        $this->assertTrue($identity->refresh()->hasTwoFactorEnabled());
        $this->assertNotNull($identity->two_factor_recovery_codes);

        Livewire::test(TwoFactorSettings::class)
            ->call('disable')
            ->assertHasNoErrors();

        $this->assertFalse($identity->refresh()->hasTwoFactorEnabled());
    }

    private function twoFactorIdentity(): Identity
    {
        $identity = $this->identity();

        app(EnableTwoFactorAuthAction::class)->handle(
            identity: $identity,
            secret: app(TwoFactorAuthenticator::class)->generateSecret(),
            recoveryCodes: ['RECOVERY-ONE', 'RECOVERY-TWO'],
            actor: $identity,
            ip: null,
            userAgent: null,
        );

        return $identity->refresh();
    }

    private function identity(): Identity
    {
        return Identity::query()->create([
            'email' => 'admin@example.test',
            'password' => 'secret-password',
            'is_super_admin' => true,
            'status' => IdentityStatus::Active,
        ]);
    }
}
