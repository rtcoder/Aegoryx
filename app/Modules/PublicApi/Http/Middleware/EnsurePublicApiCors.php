<?php

namespace App\Modules\PublicApi\Http\Middleware;

use App\Models\Landlord\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsurePublicApiCors
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $origin = $request->headers->get('Origin');
        $allowedOrigin = $this->allowedOrigin($request, $origin);

        if ($origin !== null && $allowedOrigin === null) {
            abort(403);
        }

        if ($request->isMethod('OPTIONS')) {
            return $this->withCorsHeaders(response()->noContent(), $allowedOrigin);
        }

        return $this->withCorsHeaders($next($request), $allowedOrigin);
    }

    private function withCorsHeaders(Response $response, ?string $allowedOrigin): Response
    {
        if ($allowedOrigin === null) {
            $response->headers->remove('Access-Control-Allow-Origin');
            $response->headers->remove('Access-Control-Allow-Methods');
            $response->headers->remove('Access-Control-Allow-Headers');
            $response->headers->remove('Access-Control-Max-Age');

            return $response;
        }

        $response->headers->set('Access-Control-Allow-Origin', $allowedOrigin);
        $response->headers->set('Access-Control-Allow-Methods', 'GET, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept');
        $response->headers->set('Access-Control-Max-Age', '600');
        $response->headers->set('Vary', 'Origin');

        return $response;
    }

    private function allowedOrigin(Request $request, ?string $origin): ?string
    {
        $tenant = $request->attributes->get('tenant');
        $allowedOrigins = $tenant instanceof Tenant && $tenant->public_api_cors_allowed_origins !== null
            ? $tenant->public_api_cors_allowed_origins
            : config('aegoryx.public_api.cors.allowed_origins', ['*']);

        if (in_array('*', $allowedOrigins, true)) {
            return $origin ?: '*';
        }

        if ($origin !== null && in_array($origin, $allowedOrigins, true)) {
            return $origin;
        }

        return null;
    }
}
