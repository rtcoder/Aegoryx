<?php

namespace App\Modules\PublicApi\Http\Middleware;

use App\Models\Landlord\TenantDomain;
use App\Modules\Tenancy\Enums\TenantDomainStatus;
use App\Modules\Tenancy\Enums\TenantStatus;
use App\Services\Tenancy\TenancyManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class ResolvePublicTenantFromDomain
{
    public function __construct(
        private TenancyManager $tenancy,
    ) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->getHost() === config('aegoryx.landlord.domain')) {
            abort(404);
        }

        $domain = TenantDomain::query()
            ->with('tenant')
            ->where('domain', $request->getHost())
            ->where('status', TenantDomainStatus::Verified->value)
            ->first();

        if (! $domain || ! $domain->tenant || $domain->tenant->status !== TenantStatus::Active) {
            abort(404);
        }

        $request->attributes->set('tenant', $domain->tenant);
        $this->tenancy->initialize($domain->tenant);

        try {
            return $next($request);
        } finally {
            $this->tenancy->end();
        }
    }
}
