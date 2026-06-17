@extends('tenant.layout')

@section('title', __('tenant_settings.title').' | '.$tenant->name.' | '.__('app.tenant_panel_title'))
@section('heading', __('tenant_settings.title'))
@section('subheading', __('tenant_settings.description'))

@section('content')
    <section class="space-y-5">
        @if (session('success'))
            <div class="rounded border border-emerald-700 bg-emerald-950 px-4 py-3 text-sm text-emerald-100">
                {{ session('success') }}
            </div>
        @endif

        <x-ui.card :title="__('tenant_settings.general_title')" :subtitle="__('tenant_settings.general_description')">
            <dl class="grid gap-4 text-sm md:grid-cols-2">
                <div>
                    <dt class="ui-label">{{ __('tenant_settings.name') }}</dt>
                    <dd class="mt-1 text-neutral-100">{{ $tenant->name }}</dd>
                </div>
                <div>
                    <dt class="ui-label">{{ __('tenant_settings.slug') }}</dt>
                    <dd class="mt-1 font-mono text-neutral-100">{{ $tenant->slug }}</dd>
                </div>
                <div>
                    <dt class="ui-label">{{ __('tenant_settings.status') }}</dt>
                    <dd class="mt-1"><x-ui.badge>{{ $tenant->status->value }}</x-ui.badge></dd>
                </div>
                <div>
                    <dt class="ui-label">{{ __('tenant_settings.deployment_type') }}</dt>
                    <dd class="mt-1"><x-ui.badge>{{ $tenant->deployment_type->value }}</x-ui.badge></dd>
                </div>
                <div>
                    <dt class="ui-label">{{ __('tenant_settings.billing_model') }}</dt>
                    <dd class="mt-1"><x-ui.badge>{{ $tenant->billing_model->value }}</x-ui.badge></dd>
                </div>
                <div>
                    <dt class="ui-label">{{ __('tenant_settings.license_type') }}</dt>
                    <dd class="mt-1"><x-ui.badge>{{ $tenant->license_type->value }}</x-ui.badge></dd>
                </div>
            </dl>
        </x-ui.card>

        <x-ui.card :title="__('tenant_settings.localization_title')" :subtitle="__('tenant_settings.localization_description')">
            <form method="POST" action="{{ route('tenant.settings.update') }}" class="max-w-xl space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label for="locale" class="ui-label">{{ __('tenant_settings.default_locale') }}</label>
                    <select id="locale" name="locale" @disabled(! $canManageSettings) class="ui-select mt-2">
                        @foreach ($localeOptions as $localeValue => $localeLabel)
                            <option value="{{ $localeValue }}" @selected(old('locale', $tenant->locale->value) === $localeValue)>
                                {{ $localeLabel }}
                            </option>
                        @endforeach
                    </select>
                    <p class="ui-help">{{ __('tenant_settings.default_locale_help') }}</p>
                    @error('locale') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                @if ($canManageSettings)
                    <x-ui.button type="submit">{{ __('tenant_settings.save') }}</x-ui.button>
                @else
                    <p class="text-sm text-neutral-500">{{ __('tenant_settings.read_only') }}</p>
                @endif
            </form>
        </x-ui.card>

        <x-ui.card :title="__('tenant_settings.billing_title')" :subtitle="__('tenant_settings.billing_description')">
            <dl class="grid gap-4 text-sm md:grid-cols-2">
                <div>
                    <dt class="ui-label">{{ __('tenant_settings.subscription_status') }}</dt>
                    <dd class="mt-1"><x-ui.badge>{{ $latestSubscription?->status->value ?? __('common.not_set') }}</x-ui.badge></dd>
                </div>
                <div>
                    <dt class="ui-label">{{ __('tenant_settings.plan') }}</dt>
                    <dd class="mt-1 text-neutral-100">{{ $latestSubscription?->plan?->name ?? __('common.not_set') }}</dd>
                </div>
                <div>
                    <dt class="ui-label">{{ __('tenant_settings.current_period_ends_at') }}</dt>
                    <dd class="mt-1 text-neutral-100">{{ $latestSubscription?->current_period_ends_at?->format('Y-m-d') ?? __('common.not_set') }}</dd>
                </div>
                <div>
                    <dt class="ui-label">{{ __('tenant_settings.license_status') }}</dt>
                    <dd class="mt-1"><x-ui.badge>{{ $latestLicense?->status->value ?? __('common.not_set') }}</x-ui.badge></dd>
                </div>
                <div>
                    <dt class="ui-label">{{ __('tenant_settings.license_expires_at') }}</dt>
                    <dd class="mt-1 text-neutral-100">{{ $latestLicense?->expires_at?->format('Y-m-d') ?? __('common.not_set') }}</dd>
                </div>
                <div>
                    <dt class="ui-label">{{ __('tenant_settings.license_last_verified_at') }}</dt>
                    <dd class="mt-1 text-neutral-100">{{ $latestLicense?->last_verified_at?->format('Y-m-d H:i') ?? __('common.not_set') }}</dd>
                </div>
            </dl>
        </x-ui.card>

        <x-ui.card :title="__('tenant_settings.domains_title')" :subtitle="__('tenant_settings.domains_description')">
            <div class="space-y-5">
                <div class="overflow-x-auto rounded border border-[var(--ui-border)]">
                    <table class="ui-table">
                        <thead>
                            <tr>
                                <th>{{ __('tenant_settings.domain') }}</th>
                                <th>{{ __('tenant_settings.domain_type') }}</th>
                                <th>{{ __('tenant_settings.domain_status') }}</th>
                                <th>{{ __('tenant_settings.verification_record') }}</th>
                                <th>{{ __('tenant_settings.verified_at') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($domains as $domain)
                                <tr>
                                    <td class="font-mono text-[var(--ui-text)]">{{ $domain->domain }}</td>
                                    <td><x-ui.badge>{{ __("tenant_settings.domain_types.{$domain->type->value}") }}</x-ui.badge></td>
                                    <td><x-ui.badge>{{ __("tenant_settings.domain_statuses.{$domain->status->value}") }}</x-ui.badge></td>
                                    <td class="font-mono text-xs text-[var(--ui-text-muted)]">
                                        @if ($domain->verification_token)
                                            _aegoryx-domain.{{ $domain->domain }} TXT {{ $domain->verification_token }}
                                        @else
                                            {{ __('common.not_set') }}
                                        @endif
                                    </td>
                                    <td class="text-[var(--ui-text-muted)]">{{ $domain->verified_at?->format('Y-m-d H:i') ?? __('common.not_set') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-10 text-center text-[var(--ui-text-muted)]">{{ __('tenant_settings.no_domains') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($canManageSettings)
                    <form method="POST" action="{{ route('tenant.settings.domains.store') }}" class="max-w-xl space-y-4">
                        @csrf

                        <div>
                            <label for="domain" class="ui-label">{{ __('tenant_settings.request_domain') }}</label>
                            <input id="domain" name="domain" value="{{ old('domain') }}" class="ui-input mt-2" placeholder="portal.example.com">
                            <p class="ui-help">{{ __('tenant_settings.request_domain_help') }}</p>
                            @error('domain') <p class="ui-error">{{ $message }}</p> @enderror
                        </div>

                        <x-ui.button type="submit">{{ __('tenant_settings.request_domain_submit') }}</x-ui.button>
                    </form>
                @else
                    <p class="text-sm text-neutral-500">{{ __('tenant_settings.domains_read_only') }}</p>
                @endif
            </div>
        </x-ui.card>
    </section>
@endsection
