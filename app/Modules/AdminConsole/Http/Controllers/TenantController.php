<?php

namespace App\Modules\AdminConsole\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\System\Tenant;
use App\Modules\AdminConsole\Actions\UpdateTenantStatusAction;
use App\Modules\AdminConsole\Http\Requests\Tenants\UpdateTenantStatusRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class TenantController extends Controller
{
    public function index(): View
    {
        return view('admin.tenants.index');
    }

    public function show(Tenant $tenant): View
    {
        return view('admin.tenants.show', [
            'tenant' => $tenant,
        ]);
    }

    public function updateStatus(
        UpdateTenantStatusRequest $request,
        Tenant $tenant,
        UpdateTenantStatusAction $action,
    ): RedirectResponse {
        $action->handle(
            tenant: $tenant,
            status: $request->status(),
            actor: $request->user('admin'),
            ip: $request->ip(),
            userAgent: $request->userAgent(),
        );

        return redirect()
            ->route('admin.tenants.show', $tenant)
            ->with('success', __('flash.tenant_status_updated'));
    }
}
