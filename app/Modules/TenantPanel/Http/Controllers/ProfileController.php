<?php

namespace App\Modules\TenantPanel\Http\Controllers;

use App\Models\Tenant\User;
use App\Support\Localization\Locale;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;

final class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('tenant.profile.edit', [
            'localeOptions' => $this->localeOptions(),
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'locale' => ['required', 'string', Rule::in(Locale::values())],
        ]);

        /** @var User $user */
        $user = $request->user();
        $user->forceFill([
            'name' => $validated['name'],
            'locale' => Locale::from($validated['locale']),
            'updated_by' => $user->id,
        ])->save();

        app()->setLocale($user->locale->value);

        return redirect()
            ->route('tenant.profile.edit')
            ->with('success', __('tenant_profile.updated'));
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
