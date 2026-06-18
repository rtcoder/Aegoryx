<?php

namespace App\Console\Commands;

use App\Models\Landlord\BillingEvent;
use App\Models\Landlord\Identity;
use App\Models\Landlord\Tenant;
use App\Models\Landlord\TenantDomain;
use App\Modules\Billing\Enums\BillingEventStatus;
use App\Modules\Identity\Enums\IdentityStatus;
use App\Modules\Tenancy\Enums\TenantDomainStatus;
use App\Modules\Tenancy\Enums\TenantStatus;
use Illuminate\Console\Command;

final class OpsReportCommand extends Command
{
    protected $signature = 'aegoryx:ops-report
        {--json : Output the report as JSON}';

    protected $description = 'Show an operational snapshot for the Aegoryx landlord system.';

    public function handle(): int
    {
        $report = $this->report();

        if ($this->option('json')) {
            $this->output->writeln((string) json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return self::SUCCESS;
        }

        $this->table(
            ['Metric', 'Value'],
            collect($report)->map(fn (int $value, string $label): array => [$label, $value])->values()->all(),
        );

        return self::SUCCESS;
    }

    /**
     * @return array<string, int>
     */
    private function report(): array
    {
        return [
            'tenants_total' => (int) Tenant::query()->count(),
            'tenants_active' => (int) Tenant::query()->where('status', TenantStatus::Active)->count(),
            'tenants_suspended' => (int) Tenant::query()->where('status', TenantStatus::Suspended)->count(),
            'domains_pending' => (int) TenantDomain::query()->where('status', TenantDomainStatus::Pending)->count(),
            'domains_verified' => (int) TenantDomain::query()->where('status', TenantDomainStatus::Verified)->count(),
            'superadmins_active' => (int) Identity::query()
                ->where('is_super_admin', true)
                ->where('status', IdentityStatus::Active)
                ->count(),
            'billing_events_failed' => (int) BillingEvent::query()->where('status', BillingEventStatus::Failed)->count(),
        ];
    }
}
