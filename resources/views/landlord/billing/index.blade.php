@extends('landlord.layout')

@section('title', __('common.billing').' | '.__('app.admin_title'))
@section('heading', __('common.billing'))
@section('subheading', __('landlord.sections.billing'))

@section('content')
    <div class="grid gap-5 lg:grid-cols-2">
        <section class="ui-card">
            <div class="ui-card-header">
                <h2 class="ui-heading-2">{{ __('landlord.billing.subscription_statuses') }}</h2>
            </div>
            <div class="ui-card-body space-y-3">
                @forelse ($subscriptionStatusCounts as $status => $count)
                    <div class="flex items-center justify-between rounded border border-[var(--ui-border)] px-4 py-3">
                        <x-ui.badge>{{ $status }}</x-ui.badge>
                        <span class="font-mono text-sm text-[var(--ui-text-muted)]">{{ $count }}</span>
                    </div>
                @empty
                    <x-ui.empty-state :title="__('landlord.billing.no_subscriptions')" />
                @endforelse
            </div>
        </section>

        <section class="ui-card">
            <div class="ui-card-header">
                <h2 class="ui-heading-2">{{ __('landlord.billing.license_statuses') }}</h2>
            </div>
            <div class="ui-card-body space-y-3">
                @forelse ($licenseStatusCounts as $status => $count)
                    <div class="flex items-center justify-between rounded border border-[var(--ui-border)] px-4 py-3">
                        <x-ui.badge>{{ $status }}</x-ui.badge>
                        <span class="font-mono text-sm text-[var(--ui-text-muted)]">{{ $count }}</span>
                    </div>
                @empty
                    <x-ui.empty-state :title="__('landlord.billing.no_licenses')" />
                @endforelse
            </div>
        </section>
    </div>

    <section class="ui-card mt-5">
        <div class="ui-card-header">
            <h2 class="ui-heading-2">{{ __('landlord.billing.recent_events') }}</h2>
            <p class="ui-body mt-1">{{ __('landlord.billing.recent_events_description') }}</p>
        </div>

        <div class="overflow-x-auto">
            <table class="ui-table">
                <thead>
                    <tr>
                        <th>{{ __('common.created_at') }}</th>
                        <th>{{ __('common.tenant') }}</th>
                        <th>{{ __('landlord.billing.provider') }}</th>
                        <th>{{ __('landlord.billing.event_type') }}</th>
                        <th>{{ __('common.status') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($billingEvents as $event)
                        <tr>
                            <td class="whitespace-nowrap text-[var(--ui-text-muted)]">{{ $event->created_at?->format('Y-m-d H:i') }}</td>
                            <td class="text-[var(--ui-text)]">{{ $event->tenant?->name ?? __('common.unassigned') }}</td>
                            <td class="font-mono text-xs text-[var(--ui-text-muted)]">{{ $event->provider }}</td>
                            <td class="font-mono text-xs text-[var(--ui-text-muted)]">{{ $event->event_type }}</td>
                            <td><x-ui.badge>{{ $event->status->value }}</x-ui.badge></td>
                            <td class="text-right">
                                <a href="{{ route('landlord.billing.events.show', $event) }}" class="ui-link">
                                    {{ __('landlord.billing.details') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-ui.empty-state :title="__('landlord.billing.no_events')" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
