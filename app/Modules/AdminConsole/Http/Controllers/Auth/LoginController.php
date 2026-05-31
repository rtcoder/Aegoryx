<?php

namespace App\Modules\AdminConsole\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Modules\AdminConsole\Http\Requests\LandlordLoginRequest;
use App\Modules\Identity\Enums\IdentityStatus;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class LoginController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (Auth::guard('landlord')->check()) {
            return redirect()->route('landlord.dashboard');
        }

        return view('landlord.auth.login');
    }

    public function store(LandlordLoginRequest $request): RedirectResponse
    {
        $credentials = [
            'email' => $request->string('email')->lower()->toString(),
            'password' => $request->string('password')->toString(),
            'is_super_admin' => true,
            'status' => IdentityStatus::Active->value,
        ];

        if (! Auth::guard('landlord')->attempt($credentials)) {
            return back()
                ->withErrors(['email' => 'Invalid landlord credentials.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('landlord.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('landlord')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landlord.login');
    }
}
