<?php

namespace Tests\Feature\Identity;

use App\Livewire\Landlord\Auth\LoginForm;
use App\Livewire\Landlord\Auth\TwoFactorChallengeForm;
use App\Livewire\Landlord\Security\TwoFactorSettings;
use App\Models\Landlord\Identity;
use App\Modules\Identity\Actions\EnableTwoFactorAuthAction;
use App\Modules\Identity\Enums\IdentityStatus;
use App\Modules\Identity\Support\TwoFactorAuthenticator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\TestCase;

final class LandlordTwoFactorLoginTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh', [
            '--database' => 'sqlite',
            '--path' => 'database/migrations/landlord',
        ]);
    }

    public function test_landlord_login_requires_two_factor_challenge_when_enabled(): void
    {
        $identity = $this->twoFactorIdentity();

        Livewire::test(LoginForm::class)
            ->set('email', $identity->email)
            ->set('password', 'secret-password')
            ->call('login')
            ->assertRedirect(route('landlord.two-factor.challenge'));

        $this->assertFalse(Auth::guard('landlord')->check());
        $this->assertSame($identity->id, session('landlord_login_2fa_identity_id'));
    }

    public function test_landlord_can_complete_two_factor_challenge_with_totp_code(): void
    {
        $identity = $this->twoFactorIdentity();
        $this->withSession(['landlord_login_2fa_identity_id' => $identity->id]);

        Livewire::test(TwoFactorChallengeForm::class)
            ->set('code', app(TwoFactorAuthenticator::class)->currentCode($identity->two_factor_secret))
            ->call('verify')
            ->assertRedirect(route('landlord.dashboard'));

        $this->assertTrue(Auth::guard('landlord')->check());
        $this->assertNull(session('landlord_login_2fa_identity_id'));
    }

    public function test_landlord_can_complete_two_factor_challenge_with_recovery_code_once(): void
    {
        $identity = $this->twoFactorIdentity();
        $this->withSession(['landlord_login_2fa_identity_id' => $identity->id]);

        Livewire::test(TwoFactorChallengeForm::class)
            ->set('code', 'RECOVERY-ONE')
            ->call('verify')
            ->assertRedirect(route('landlord.dashboard'));

        $this->assertTrue(Auth::guard('landlord')->check());
        $this->assertCount(1, $identity->refresh()->two_factor_recovery_codes);
    }

    public function test_security_screen_can_enable_and_disable_two_factor(): void
    {
        $identity = $this->identity();
        $this->actingAs($identity, 'landlord');

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
