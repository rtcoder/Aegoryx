@extends('tenant.layout')

@section('title', __('crm.deals').' | '.$tenant->name.' | '.__('app.tenant_panel_title'))
@section('heading', __('crm.deals'))
@section('subheading', __('crm.deals_description'))

@section('content')
    @include('tenant.crm.partials.navigation')

    <div class="grid gap-5 xl:grid-cols-[360px_1fr]">
        <x-ui.card :title="__('crm.create_deal')">
            <form id="create-deal" method="POST" action="{{ route('tenant.crm.deals.store') }}" class="mt-5 space-y-4">
                @csrf

                @include('tenant.crm.deals.partials.fields')

                <x-ui.button type="submit" class="w-full">
                    {{ __('crm.create_deal') }}
                </x-ui.button>
            </form>
        </x-ui.card>

        <section class="ui-card">
            <div class="ui-card-header">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <h2 class="ui-heading-2">{{ __('crm.deal_list') }}</h2>
                        <p class="ui-body mt-1">{{ __('crm.deals_description') }}</p>
                    </div>
                    <form method="GET" action="{{ route('tenant.crm.deals.index') }}" class="flex flex-col gap-2 sm:flex-row">
                        <input name="search" value="{{ $search }}" class="ui-input min-w-64" placeholder="{{ __('common.search_placeholder') }}">
                        <x-ui.button type="submit" variant="secondary">{{ __('common.search') }}</x-ui.button>
                        @if ($search !== '')
                            <x-ui.button :href="route('tenant.crm.deals.index')" variant="ghost">{{ __('common.clear_search') }}</x-ui.button>
                        @endif
                    </form>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th><x-table.sort-link sort="title" :current-sort="$sort" :current-direction="$direction">{{ __('crm.deal_title') }}</x-table.sort-link></th>
                            <th>{{ __('crm.company') }}</th>
                            <th><x-table.sort-link sort="status" :current-sort="$sort" :current-direction="$direction">{{ __('common.status') }}</x-table.sort-link></th>
                            <th><x-table.sort-link sort="value" :current-sort="$sort" :current-direction="$direction">{{ __('crm.value_amount') }}</x-table.sort-link></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($deals as $deal)
                            @php
                                $badgeVariant = match ($deal->status) {
                                    \App\Modules\Crm\Enums\CrmDealStatus::Won => 'success',
                                    \App\Modules\Crm\Enums\CrmDealStatus::Lost => 'danger',
                                    default => 'accent',
                                };
                            @endphp
                            <tr>
                                <td class="font-medium text-[var(--ui-text)]">{{ $deal->title }}</td>
                                <td class="text-[var(--ui-text-muted)]">{{ $deal->company?->name ?? __('common.not_set') }}</td>
                                <td>
                                    <x-ui.badge :variant="$badgeVariant">
                                        {{ __("crm.deal_status.{$deal->status->value}") }}
                                    </x-ui.badge>
                                </td>
                                <td class="text-[var(--ui-text-muted)]">
                                    @if ($deal->value_amount)
                                        {{ $deal->value_amount }} {{ $deal->currency }}
                                    @else
                                        {{ __('common.not_set') }}
                                    @endif
                                </td>
                                <td class="text-right">
                                    <div class="inline-flex items-center gap-3">
                                        <a href="{{ route('tenant.crm.deals.edit', $deal) }}" wire:navigate class="ui-link">
                                            {{ __('common.manage') }}
                                        </a>
                                        <form method="POST" action="{{ route('tenant.crm.deals.destroy', $deal) }}">
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
                                <td colspan="5" class="py-10 text-center text-[var(--ui-text-muted)]">
                                    @if ($search === '')
                                        <x-ui.empty-state :title="__('crm.no_deals')" :action-label="__('crm.create_deal')" action-href="#create-deal" />
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
                {{ $deals->links() }}
            </div>
        </section>
    </div>
@endsection
