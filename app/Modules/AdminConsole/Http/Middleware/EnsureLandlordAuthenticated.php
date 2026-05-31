<?php

namespace App\Modules\AdminConsole\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

final class EnsureLandlordAuthenticated
{
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        if (! Auth::guard('landlord')->check()) {
            return redirect()->route('landlord.login');
        }

        return $next($request);
    }
}
