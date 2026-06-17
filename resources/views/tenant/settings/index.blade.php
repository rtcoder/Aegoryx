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
    </section>
@endsection
