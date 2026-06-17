<?php

namespace App\Modules\AdminConsole\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Landlord\Identity;
use App\Modules\AdminConsole\Http\Requests\LandlordLoginRequest;
use App\Modules\Identity\Enums\IdentityStatus;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

        $identity = Identity::query()
            ->where('email', $credentials['email'])
            ->where('is_super_admin', true)
            ->where('status', IdentityStatus::Active->value)
            ->first();

        if (! $identity || ! Hash::check($credentials['password'], $identity->password)) {
            return back()
                ->withErrors(['email' => 'Invalid landlord credentials.'])
                ->onlyInput('email');
        }

        if ($identity->hasTwoFactorEnabled()) {
            session(['landlord_login_2fa_identity_id' => $identity->id]);

            return redirect()->route('landlord.two-factor.challenge');
        }

        Auth::guard('landlord')->login($identity);
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
