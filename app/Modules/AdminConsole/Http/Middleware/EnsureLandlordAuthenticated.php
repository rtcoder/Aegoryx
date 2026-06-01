<?php

namespace App\Modules\AdminConsole\Http\Middleware;

use App\Modules\Identity\Enums\IdentityStatus;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class EnsureLandlordAuthenticated
{
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        if (! Auth::guard('landlord')->check()) {
            return redirect()->route('landlord.login');
        }

        $identity = Auth::guard('landlord')->user();

        if (! $identity?->is_super_admin || $identity->status !== IdentityStatus::Active) {
            Auth::guard('landlord')->logout();

            throw new HttpException(403, __('errors.403_default'));
        }

        return $next($request);
    }
}
