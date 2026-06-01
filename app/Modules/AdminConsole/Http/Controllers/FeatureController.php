<?php

namespace App\Modules\AdminConsole\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Landlord\Feature;
use App\Models\Landlord\Tenant;
use App\Modules\AdminConsole\Http\Requests\Features\SetTenantFeatureOverrideRequest;
use App\Modules\AdminConsole\Http\Requests\Features\StoreFeatureRequest;
use App\Modules\AdminConsole\Http\Requests\Features\UpdateFeatureStatusRequest;
use App\Modules\Entitlements\Actions\CreateFeatureAction;
use App\Modules\Entitlements\Actions\SetTenantFeatureOverrideAction;
use App\Modules\Entitlements\Actions\UpdateFeatureStatusAction;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class FeatureController extends Controller
{
    public function index(): View
    {
        return view('landlord.features.index');
    }

    public function store(StoreFeatureRequest $request, CreateFeatureAction $action): RedirectResponse
    {
        $feature = $action->handle(
            key: $request->string('key')->toString(),
            name: $request->string('name')->toString(),
            description: $request->string('description')->toString() ?: null,
            status: $request->status(),
            actor: $request->user('landlord'),
            ip: $request->ip(),
            userAgent: $request->userAgent(),
        );

        return redirect()
            ->route('landlord.features.show', $feature)
            ->with('success', 'Feature created.');
    }

    public function show(Feature $feature): View
    {
        return view('landlord.features.show', [
            'feature' => $feature,
        ]);
    }

    public function updateStatus(
        UpdateFeatureStatusRequest $request,
        Feature $feature,
        UpdateFeatureStatusAction $action,
    ): RedirectResponse {
        $action->handle(
            feature: $feature,
            status: $request->status(),
            actor: $request->user('landlord'),
            ip: $request->ip(),
            userAgent: $request->userAgent(),
        );

        return redirect()
            ->route('landlord.features.show', $feature)
            ->with('success', 'Feature status updated.');
    }

    public function setTenantOverride(
        SetTenantFeatureOverrideRequest $request,
        Feature $feature,
        SetTenantFeatureOverrideAction $action,
    ): RedirectResponse {
        $tenant = Tenant::query()->findOrFail($request->integer('tenant_id'));

        $action->handle(
            tenant: $tenant,
            feature: $feature,
            enabled: $request->enabled(),
            reason: $request->string('reason')->toString(),
            actor: $request->user('landlord'),
            ip: $request->ip(),
            userAgent: $request->userAgent(),
        );

        return redirect()
            ->route('landlord.features.show', $feature)
            ->with('success', 'Tenant feature override saved.');
    }
}
