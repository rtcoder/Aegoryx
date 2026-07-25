<?php

namespace App\Modules\AdminConsole\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\System\License;
use App\Modules\AdminConsole\Http\Requests\Licenses\VerifyLicenseRequest;
use App\Modules\Licensing\Actions\VerifyLicenseAction;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class LicenseController extends Controller
{
    public function index(): View
    {
        return view('admin.licenses.index');
    }

    public function show(License $license): View
    {
        return view('admin.licenses.show', [
            'license' => $license,
        ]);
    }

    public function verify(
        VerifyLicenseRequest $request,
        License $license,
        VerifyLicenseAction $action,
    ): RedirectResponse {
        $action->handle(
            license: $license,
            actor: $request->user('admin'),
            ip: $request->ip(),
            userAgent: $request->userAgent(),
        );

        return redirect()
            ->route('admin.licenses.show', $license)
            ->with('success', __('flash.license_verified'));
    }
}
