<?php

namespace App\Console\Commands;

use App\Models\Landlord\BillingEvent;
use App\Models\Landlord\Identity;
use App\Modules\Billing\Enums\BillingEventStatus;
use App\Modules\Identity\Enums\IdentityStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Throwable;

final class LaunchCheckCommand extends Command
{
    protected $signature = 'aegoryx:launch-check
        {--skip-db : Skip database-dependent launch checks}
        {--with-smoke : Run HTTP smoke checks after static and database checks}';

    protected $description = 'Run MVP launch readiness checks for the Aegoryx application.';

    public function handle(): int
    {
        $checks = [
            'Preflight checks pass' => $this->preflightPasses(),
            'Required launch documents exist' => $this->requiredDocumentsExist(),
            'Security and privacy audits have no blockers' => $this->auditsHaveNoBlockers(),
            'Required operational commands are registered' => $this->requiredCommandsAreRegistered(),
            'Public API routes are registered' => $this->publicApiRoutesAreRegistered(),
            'Horizon access is protected by landlord auth and superadmin gate' => $this->horizonAccessIsProtected(),
        ];

        if (! $this->option('skip-db')) {
            $checks['Active landlord superadmin exists'] = $this->activeSuperadminExists();
            $checks['No failed billing events are waiting for retry'] = $this->hasNoFailedBillingEvents();
        }

        if ($this->option('with-smoke')) {
            $checks['Smoke checks pass'] = $this->smokePasses();
        } else {
            $this->components->warn('Smoke checks skipped; pass --with-smoke to run HTTP checks.');
        }

        $failed = false;

        foreach ($checks as $label => $passed) {
            if ($passed) {
                $this->components->info($label);

                continue;
            }

            $failed = true;
            $this->components->error($label);
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }

    private function preflightPasses(): bool
    {
        return Artisan::call('aegoryx:preflight', [
            '--skip-db' => (bool) $this->option('skip-db'),
        ]) === self::SUCCESS;
    }

    private function requiredDocumentsExist(): bool
    {
        foreach ($this->requiredDocumentPaths() as $path) {
            if (! is_file(base_path($path))) {
                return false;
            }
        }

        return true;
    }

    private function auditsHaveNoBlockers(): bool
    {
        foreach ([
            'docs/security/authorization-audit.md',
            'docs/security/privacy-audit.md',
        ] as $path) {
            $contents = file_get_contents(base_path($path));

            if (! is_string($contents) || ! str_contains($contents, 'Brak Blockerów')) {
                return false;
            }
        }

        return true;
    }

    private function requiredCommandsAreRegistered(): bool
    {
        $commands = array_keys(Artisan::all());

        foreach ([
            'landlord:create',
            'aegoryx:preflight',
            'aegoryx:smoke',
            'aegoryx:launch-check',
            'aegoryx:retention:purge',
            'tenant-domains:verify',
        ] as $command) {
            if (! in_array($command, $commands, true)) {
                return false;
            }
        }

        return true;
    }

    private function publicApiRoutesAreRegistered(): bool
    {
        return Route::has('public-api.v1.cms.pages.show')
            && Route::has('public-api.cms.pages.show');
    }

    private function horizonAccessIsProtected(): bool
    {
        $superadmin = new Identity([
            'email' => 'launch-check@example.test',
            'is_super_admin' => true,
            'status' => IdentityStatus::Active,
        ]);

        return in_array('auth:landlord', (array) config('horizon.middleware'), true)
            && Gate::forUser($superadmin)->allows('viewHorizon');
    }

    private function activeSuperadminExists(): bool
    {
        try {
            return Identity::query()
                ->where('is_super_admin', true)
                ->where('status', IdentityStatus::Active)
                ->whereNotNull('password')
                ->exists();
        } catch (Throwable) {
            return false;
        }
    }

    private function hasNoFailedBillingEvents(): bool
    {
        try {
            return ! BillingEvent::query()
                ->where('status', BillingEventStatus::Failed)
                ->exists();
        } catch (Throwable) {
            return false;
        }
    }

    private function smokePasses(): bool
    {
        return Artisan::call('aegoryx:smoke') === self::SUCCESS;
    }

    /**
     * @return array<int, string>
     */
    private function requiredDocumentPaths(): array
    {
        return [
            'docs/security/authorization-audit.md',
            'docs/security/privacy-audit.md',
            'docs/operations/backup-restore.md',
            'docs/operations/deploy.md',
            'docs/product/mvp-launch-checklist.md',
            'docs/product/risk-acceptance.md',
        ];
    }
}
