<?php

namespace App\Modules\TenantPanel\Http\Controllers;

use App\Models\Landlord\Tenant;
use App\Support\Localization\Locale;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;

final class SettingsController extends Controller
{
    public function index(Request $request): View
    {
        /** @var Tenant $tenant */
        $tenant = $request->attributes->get('tenant');

        return view('tenant.settings.index', [
            'canManageSettings' => $request->user()?->canManageTenantSettings() === true,
            'localeOptions' => $this->localeOptions(),
            'tenant' => $tenant,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->canManageTenantSettings() === true, 403);

        $validated = $request->validate([
            'locale' => ['required', 'string', Rule::in(Locale::values())],
        ]);

        /** @var Tenant $tenant */
        $tenant = $request->attributes->get('tenant');
        $tenant->forceFill([
            'locale' => Locale::from($validated['locale']),
            'updated_by' => $request->user()?->id,
        ])->save();

        app()->setLocale($tenant->locale->value);

        return back()->with('success', __('tenant_settings.updated'));
    }

    /**
     * @return array<string, string>
     */
    private function localeOptions(): array
    {
        return collect(Locale::cases())
            ->mapWithKeys(fn (Locale $locale): array => [
                $locale->value => __("tenant_settings.locales.{$locale->value}"),
            ])
            ->all();
    }
}
