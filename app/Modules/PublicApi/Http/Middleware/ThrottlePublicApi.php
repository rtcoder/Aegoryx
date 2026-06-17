<?php

namespace App\Modules\PublicApi\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

final readonly class ThrottlePublicApi
{
    public function __construct(
        private RateLimiter $limiter,
    ) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = $this->key($request);
        $maxAttempts = (int) config('aegoryx.public_api.rate_limit.max_attempts', 60);
        $decaySeconds = (int) config('aegoryx.public_api.rate_limit.decay_seconds', 60);

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            $retryAfter = $this->limiter->availableIn($key);

            return response()->json([
                'message' => 'Too Many Attempts.',
            ], 429)->withHeaders([
                'Retry-After' => $retryAfter,
                'X-RateLimit-Limit' => $maxAttempts,
                'X-RateLimit-Remaining' => 0,
            ]);
        }

        $this->limiter->hit($key, $decaySeconds);

        $response = $next($request);
        $response->headers->set('X-RateLimit-Limit', (string) $maxAttempts);
        $response->headers->set('X-RateLimit-Remaining', (string) $this->limiter->remaining($key, $maxAttempts));

        return $response;
    }

    private function key(Request $request): string
    {
        return 'public-api:'.sha1(Str::lower($request->getHost()).'|'.$request->ip());
    }
}
