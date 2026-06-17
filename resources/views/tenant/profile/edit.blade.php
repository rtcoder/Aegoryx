@extends('tenant.layout')

@section('title', __('tenant_profile.title').' | '.__('app.tenant_panel_title'))
@section('heading', __('tenant_profile.title'))
@section('subheading', __('tenant_profile.description'))

@section('content')
    <section class="space-y-5">
        @if (session('success'))
            <div class="rounded border border-emerald-700 bg-emerald-950 px-4 py-3 text-sm text-emerald-100">
                {{ session('success') }}
            </div>
        @endif

        <x-ui.card :title="__('tenant_profile.account_title')" :subtitle="__('tenant_profile.account_description')">
            <form method="POST" action="{{ route('tenant.profile.update') }}" class="max-w-xl space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label for="name" class="ui-label">{{ __('tenant_profile.name') }}</label>
                    <input id="name" name="name" value="{{ old('name', $user->name) }}" class="ui-input mt-2">
                    @error('name') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="locale" class="ui-label">{{ __('tenant_profile.locale') }}</label>
                    <select id="locale" name="locale" class="ui-select mt-2">
                        @foreach ($localeOptions as $localeValue => $localeLabel)
                            <option value="{{ $localeValue }}" @selected(old('locale', $user->locale->value) === $localeValue)>
                                {{ $localeLabel }}
                            </option>
                        @endforeach
                    </select>
                    <p class="ui-help">{{ __('tenant_profile.locale_help') }}</p>
                    @error('locale') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <x-ui.button type="submit">{{ __('tenant_profile.save') }}</x-ui.button>
            </form>
        </x-ui.card>
    </section>
@endsection
