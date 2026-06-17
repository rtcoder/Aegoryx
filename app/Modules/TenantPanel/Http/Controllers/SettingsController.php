<?php

namespace App\Modules\TenantPanel\Http\Controllers;

use App\Models\Landlord\Tenant;
use App\Models\Landlord\TenantDomain;
use App\Modules\Tenancy\Enums\TenantDomainStatus;
use App\Modules\Tenancy\Enums\TenantDomainType;
use App\Support\Localization\Locale;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

final class SettingsController extends Controller
{
    public function index(Request $request): View
    {
        /** @var Tenant $tenant */
        $tenant = $request->attributes->get('tenant');

        return view('tenant.settings.index', [
            'canManageSettings' => $request->user()?->canManageTenantSettings() === true,
            'domains' => $tenant->domains()
                ->orderByRaw("case when type = 'primary' then 0 else 1 end")
                ->orderBy('domain')
                ->get(),
            'localeOptions' => $this->localeOptions(),
            'latestLicense' => $tenant->licenses()->latest('id')->first(),
            'latestSubscription' => $tenant->subscriptions()->with('plan')->latest('id')->first(),
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

        return redirect()
            ->route('tenant.settings.index')
            ->with('success', __('tenant_settings.updated'));
    }

    public function storeDomain(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->canManageTenantSettings() === true, 403);

        $domain = $this->normalizeDomain($request->string('domain')->toString());

        $validator = validator(
            ['domain' => $domain],
            [
                'domain' => [
                    'required',
                    'string',
                    'max:255',
                    'regex:/^(?!-)[a-z0-9.-]+(?<!-)$/',
                    'not_in:'.config('aegoryx.landlord.domain'),
                    Rule::unique('tenant_domains', 'domain'),
                ],
            ],
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        /** @var Tenant $tenant */
        $tenant = $request->attributes->get('tenant');

        TenantDomain::query()->create([
            'tenant_id' => $tenant->id,
            'domain' => $domain,
            'type' => TenantDomainType::Alias,
            'status' => TenantDomainStatus::Pending,
            'verification_token' => 'aegoryx-'.Str::random(40),
            'created_by' => $request->user()?->id,
            'updated_by' => $request->user()?->id,
        ]);

        return redirect()
            ->route('tenant.settings.index')
            ->with('success', __('tenant_settings.domain_requested'));
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

    private function normalizeDomain(string $domain): string
    {
        $domain = Str::lower(trim($domain));

        if ($domain === '') {
            return '';
        }

        $host = parse_url(Str::startsWith($domain, ['http://', 'https://']) ? $domain : "//{$domain}", PHP_URL_HOST);

        return trim((string) $host, '.');
    }
}
