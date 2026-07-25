<?php

namespace App\Modules\AdminConsole\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\System\Identity;
use App\Modules\AdminConsole\Http\Requests\SuperadminLoginRequest;
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
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function store(SuperadminLoginRequest $request): RedirectResponse
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
                ->withErrors(['email' => 'Invalid system credentials.'])
                ->onlyInput('email');
        }

        if ($identity->hasTwoFactorEnabled()) {
            session(['admin_login_2fa_identity_id' => $identity->id]);

            return redirect()->route('admin.two-factor.challenge');
        }

        Auth::guard('admin')->login($identity);
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
