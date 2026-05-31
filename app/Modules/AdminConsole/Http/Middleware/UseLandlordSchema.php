<?php

namespace App\Modules\AdminConsole\Http\Middleware;

use App\Services\Tenancy\PostgresSchemaManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class UseLandlordSchema
{
    public function __construct(
        private PostgresSchemaManager $schemas,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $this->schemas->usePublicSchema();

        try {
            return $next($request);
        } finally {
            $this->schemas->resetSearchPath();
        }
    }
}
