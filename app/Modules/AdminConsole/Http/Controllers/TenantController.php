<?php

namespace App\Modules\AdminConsole\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Landlord\Tenant;
use App\Modules\AdminConsole\Actions\UpdateTenantStatusAction;
use App\Modules\AdminConsole\Http\Requests\Tenants\UpdateTenantStatusRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class TenantController extends Controller
{
    public function index(): View
    {
        return view('landlord.tenants.index', [
            'tenants' => Tenant::query()
                ->orderBy('name')
                ->paginate(20),
        ]);
    }

    public function show(Tenant $tenant): View
    {
        return view('landlord.tenants.show', [
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
            actor: $request->user('landlord'),
            ip: $request->ip(),
            userAgent: $request->userAgent(),
        );

        return redirect()
            ->route('landlord.tenants.show', $tenant)
            ->with('success', 'Tenant status updated.');
    }
}
