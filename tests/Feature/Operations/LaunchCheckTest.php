<?php

namespace Tests\Feature\Operations;

use App\Models\Landlord\BillingEvent;
use App\Models\Landlord\Identity;
use App\Modules\Billing\Enums\BillingEventStatus;
use App\Modules\Identity\Enums\IdentityStatus;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

final class LaunchCheckTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh', [
            '--database' => 'sqlite',
            '--path' => 'database/migrations/landlord',
        ]);
    }

    public function test_launch_check_passes_for_ready_runtime_state(): void
    {
        $this->createSuperadmin();

        $exitCode = Artisan::call('aegoryx:launch-check');

        $this->assertSame(0, $exitCode);
    }

    public function test_launch_check_can_skip_database_dependent_checks(): void
    {
        $exitCode = Artisan::call('aegoryx:launch-check', [
            '--skip-db' => true,
        ]);

        $this->assertSame(0, $exitCode);
    }

    public function test_launch_check_fails_without_active_superadmin(): void
    {
        $exitCode = Artisan::call('aegoryx:launch-check');

        $this->assertSame(1, $exitCode);
    }

    public function test_launch_check_fails_when_failed_billing_events_exist(): void
    {
        $this->createSuperadmin();

        BillingEvent::query()->create([
            'provider' => 'paddle',
            'provider_event_id' => 'evt_failed_launch_check',
            'event_type' => 'subscription.updated',
            'status' => BillingEventStatus::Failed,
            'payload' => ['status' => 'past_due'],
            'failure_reason' => 'Plan mapping is missing.',
            'failed_at' => now(),
        ]);

        $exitCode = Artisan::call('aegoryx:launch-check');

        $this->assertSame(1, $exitCode);
    }

    private function createSuperadmin(): Identity
    {
        return Identity::query()->create([
            'email' => 'root@example.test',
            'password' => 'super-secret-password',
            'is_super_admin' => true,
            'status' => IdentityStatus::Active,
        ]);
    }
}
