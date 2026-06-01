<?php

namespace App\Http\Middleware;

use App\Support\Localization\Locale;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

final class UseAuthenticatedLocale
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = Auth::guard('landlord')->user()?->locale
            ?? Auth::guard('web')->user()?->locale;

        if ($locale instanceof Locale) {
            app()->setLocale($locale->value);
        }

        return $next($request);
    }
}
