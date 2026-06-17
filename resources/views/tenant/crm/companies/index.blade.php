@extends('tenant.layout')

@section('title', __('crm.companies').' | '.$tenant->name.' | '.__('app.tenant_panel_title'))
@section('heading', __('crm.companies'))
@section('subheading', __('crm.companies_description'))

@section('content')
    @include('tenant.crm.partials.navigation')

    <div class="grid gap-5 xl:grid-cols-[360px_1fr]">
        <x-ui.card :title="__('crm.create_company')">
            <form id="create-company" method="POST" action="{{ route('tenant.crm.companies.store') }}" class="mt-5 space-y-4">
                @csrf

                @include('tenant.crm.companies.partials.fields')

                <x-ui.button type="submit" class="w-full">
                    {{ __('crm.create_company') }}
                </x-ui.button>
            </form>
        </x-ui.card>

        <section class="ui-card">
            <div class="ui-card-header">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <h2 class="ui-heading-2">{{ __('crm.company_list') }}</h2>
                        <p class="ui-body mt-1">{{ __('crm.companies_description') }}</p>
                    </div>
                    <form method="GET" action="{{ route('tenant.crm.companies.index') }}" class="flex flex-col gap-2 sm:flex-row">
                        <input name="search" value="{{ $search }}" class="ui-input min-w-64" placeholder="{{ __('common.search_placeholder') }}">
                        <x-ui.button type="submit" variant="secondary">{{ __('common.search') }}</x-ui.button>
                        @if ($search !== '')
                            <x-ui.button :href="route('tenant.crm.companies.index')" variant="ghost">{{ __('common.clear_search') }}</x-ui.button>
                        @endif
                    </form>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th><x-table.sort-link sort="name" :current-sort="$sort" :current-direction="$direction">{{ __('common.name') }}</x-table.sort-link></th>
                            <th><x-table.sort-link sort="website" :current-sort="$sort" :current-direction="$direction">{{ __('crm.website') }}</x-table.sort-link></th>
                            <th>{{ __('crm.linked_contacts') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($companies as $company)
                            <tr>
                                <td class="font-medium text-[var(--ui-text)]">{{ $company->name }}</td>
                                <td class="text-[var(--ui-text-muted)]">{{ $company->website ?? __('common.not_set') }}</td>
                                <td class="text-[var(--ui-text-muted)]">
                                    {{ $company->contacts->pluck('first_name')->join(', ') ?: __('common.not_set') }}
                                </td>
                                <td class="text-right">
                                    <div class="inline-flex items-center gap-3">
                                        <a href="{{ route('tenant.crm.companies.edit', $company) }}" wire:navigate class="ui-link">
                                            {{ __('common.manage') }}
                                        </a>
                                        <form method="POST" action="{{ route('tenant.crm.companies.destroy', $company) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="font-medium text-[var(--ui-danger)] hover:brightness-110">
                                                {{ __('common.delete') }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-10 text-center text-[var(--ui-text-muted)]">
                                    @if ($search === '')
                                        <x-ui.empty-state :title="__('crm.no_companies')" :action-label="__('crm.create_company')" action-href="#create-company" />
                                    @else
                                        {{ __('common.no_results') }}
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-[var(--ui-border)] px-5 py-4">
                {{ $companies->links() }}
            </div>
        </section>
    </div>
@endsection
