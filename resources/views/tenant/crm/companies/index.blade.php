@extends('tenant.layout')

@section('title', __('crm.companies').' | '.$tenant->name.' | '.__('app.tenant_panel_title'))
@section('heading', __('crm.companies'))
@section('subheading', __('crm.companies_description'))

@section('content')
    <div class="mb-5 flex flex-wrap items-center gap-3">
        <x-ui.button :href="route('tenant.crm.index')" wire:navigate variant="secondary" size="sm">
            {{ __('crm.contacts') }}
        </x-ui.button>
        <x-ui.button :href="route('tenant.crm.companies.index')" wire:navigate size="sm">
            {{ __('crm.companies') }}
        </x-ui.button>
        <x-ui.button :href="route('tenant.crm.deals.index')" wire:navigate variant="secondary" size="sm">
            {{ __('crm.deals') }}
        </x-ui.button>
    </div>

    <div class="grid gap-5 xl:grid-cols-[360px_1fr]">
        <x-ui.card :title="__('crm.create_company')">
            <form method="POST" action="{{ route('tenant.crm.companies.store') }}" class="mt-5 space-y-4">
                @csrf

                @include('tenant.crm.companies.partials.fields')

                <x-ui.button type="submit" class="w-full">
                    {{ __('crm.create_company') }}
                </x-ui.button>
            </form>
        </x-ui.card>

        <section class="ui-card">
            <div class="ui-card-header">
                <h2 class="ui-heading-2">{{ __('crm.company_list') }}</h2>
                <p class="ui-body mt-1">{{ __('crm.companies_description') }}</p>
            </div>

            <div class="overflow-x-auto">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th>{{ __('common.name') }}</th>
                            <th>{{ __('crm.website') }}</th>
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
                                <td colspan="4" class="py-10 text-center text-[var(--ui-text-muted)]">{{ __('crm.no_companies') }}</td>
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
