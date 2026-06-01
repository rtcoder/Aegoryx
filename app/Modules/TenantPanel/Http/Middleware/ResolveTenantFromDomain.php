<?php

namespace App\Modules\TenantPanel\Http\Middleware;

use App\Models\Landlord\TenantDomain;
use App\Modules\Tenancy\Enums\TenantDomainStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ResolveTenantFromDomain
{
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

        return $next($request);
    }
}
