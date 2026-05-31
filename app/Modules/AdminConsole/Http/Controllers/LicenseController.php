<?php

namespace App\Modules\AdminConsole\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Landlord\License;
use App\Modules\AdminConsole\Http\Requests\Licenses\VerifyLicenseRequest;
use App\Modules\Licensing\Actions\VerifyLicenseAction;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class LicenseController extends Controller
{
    public function index(): View
    {
        return view('landlord.licenses.index', [
            'licenses' => License::query()
                ->with('tenant')
                ->latest()
                ->paginate(20),
        ]);
    }

    public function show(License $license): View
    {
        return view('landlord.licenses.show', [
            'license' => $license->load('tenant'),
        ]);
    }

    public function verify(
        VerifyLicenseRequest $request,
        License $license,
        VerifyLicenseAction $action,
    ): RedirectResponse {
        $action->handle(
            license: $license,
            actor: $request->user('landlord'),
            ip: $request->ip(),
            userAgent: $request->userAgent(),
        );

        return redirect()
            ->route('landlord.licenses.show', $license)
            ->with('success', 'License verified.');
    }
}
