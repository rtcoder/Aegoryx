<?php

namespace App\Modules\TenantPanel\Http\Middleware;

use App\Models\Landlord\Tenant;
use App\Modules\Entitlements\Services\EffectiveEntitlements;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class EnsureTenantFeatureEnabled
{
    public function __construct(
        private EffectiveEntitlements $entitlements,
    ) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next, string $featureKey): Response
    {
        $tenant = $request->attributes->get('tenant');

        if (! $tenant instanceof Tenant || ! $this->entitlements->allows($tenant, $featureKey)) {
            abort(403, __('errors.module_unavailable'));
        }

        return $next($request);
    }
}
