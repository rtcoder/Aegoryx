<?php

namespace App\Console\Commands;

use App\Models\Landlord\TenantDomain;
use App\Modules\Tenancy\Enums\TenantDomainStatus;
use App\Modules\Tenancy\Services\DnsTxtResolver;
use Illuminate\Console\Command;

final class VerifyTenantDomainsCommand extends Command
{
    protected $signature = 'tenant-domains:verify
        {--domain= : Verify one domain only}';

    protected $description = 'Verify pending tenant domains using DNS TXT tokens.';

    public function handle(DnsTxtResolver $dns): int
    {
        $query = TenantDomain::query()
            ->where('status', TenantDomainStatus::Pending)
            ->whereNotNull('verification_token');

        if ($domain = $this->option('domain')) {
            $query->where('domain', $domain);
        }

        $domains = $query->orderBy('domain')->get();

        if ($domains->isEmpty()) {
            $this->warn('No pending domains found.');

            return self::SUCCESS;
        }

        $verified = 0;

        foreach ($domains as $domain) {
            $host = "_aegoryx-domain.{$domain->domain}";
            $records = $dns->records($host);

            if (! in_array($domain->verification_token, $records, true)) {
                $this->warn("Domain [{$domain->domain}] is still pending.");

                continue;
            }

            $domain->forceFill([
                'status' => TenantDomainStatus::Verified,
                'verified_at' => now(),
            ])->save();

            $verified++;
            $this->info("Domain [{$domain->domain}] verified.");
        }

        $this->line("Verified domains: {$verified}");

        return self::SUCCESS;
    }
}
