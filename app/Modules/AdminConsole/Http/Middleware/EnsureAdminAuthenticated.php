<?php

namespace App\Modules\AdminConsole\Http\Middleware;

use App\Modules\Identity\Enums\IdentityStatus;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class EnsureAdminAuthenticated
{
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        if (! Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        $identity = Auth::guard('admin')->user();

        if (! $identity?->is_super_admin || $identity->status !== IdentityStatus::Active) {
            Auth::guard('admin')->logout();

            throw new HttpException(403, __('errors.403_default'));
        }

        return $next($request);
    }
}
