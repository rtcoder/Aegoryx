<?php

namespace App\Modules\TenantPanel\Http\Middleware;

use App\Models\Landlord\TenantDomain;
use App\Modules\Entitlements\Services\EffectiveEntitlements;
use App\Modules\Tenancy\Enums\TenantDomainStatus;
use App\Modules\TenantPanel\Navigation\TenantNavigation;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class ResolveTenantFromDomain
{
    public function __construct(
        private EffectiveEntitlements $entitlements,
        private TenantNavigation $navigation,
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

        if (! $domain || ! $domain->tenant) {
            abort(404);
        }

        $request->attributes->set('tenant', $domain->tenant);
        view()->share('tenant', $domain->tenant);
        view()->share('tenantEntitlements', $this->entitlements->forTenant($domain->tenant));
        view()->share('tenantNavigation', $this->navigation->visibleForTenant($domain->tenant));
        view()->share('tenantModuleCards', $this->navigation->moduleCardsForTenant($domain->tenant));

        return $next($request);
    }
}
