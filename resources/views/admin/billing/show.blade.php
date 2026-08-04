@extends('admin.layout')

@php
    $payload = json_encode($event->payload ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
@endphp

@section('title', __('admin.billing.event_details').' | '.__('app.admin_title'))
@section('heading', __('admin.billing.event_details'))
@section('subheading', __('admin.billing.event_details_description'))

@section('content')
    <div class="mb-6 flex flex-wrap items-center gap-3">
        <x-ui.button :href="route('admin.billing.index')" variant="secondary">
            {{ __('common.back') }}
        </x-ui.button>

        @if ($event->status === \App\Modules\Billing\Enums\BillingEventStatus::Failed)
            <form method="POST" action="{{ route('admin.billing.events.retry', $event) }}">
                @csrf
                <x-ui.button type="submit">
                    {{ __('admin.billing.retry_event') }}
                </x-ui.button>
            </form>
        @endif
    </div>

    @if (session('success'))
        <x-ui.alert variant="success" class="mb-6">
            {{ session('success') }}
        </x-ui.alert>
    @endif

    @if (session('error'))
        <x-ui.alert variant="danger" class="mb-6">
            {{ session('error') }}
        </x-ui.alert>
    @endif

    <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_minmax(320px,420px)]">
        <div class="ui-card">
            <div class="ui-card-header">
                <h2 class="ui-heading-2">{{ __('admin.billing.event') }}</h2>
                <p class="ui-body mt-1">{{ $event->provider_event_id }}</p>
            </div>

            <dl class="ui-card-body grid gap-4 sm:grid-cols-2">
                <div>
                    <dt class="ui-label">{{ __('admin.billing.provider') }}</dt>
                    <dd class="mt-1 font-mono text-sm text-[var(--ui-text)]">{{ $event->provider }}</dd>
                </div>
                <div>
                    <dt class="ui-label">{{ __('admin.billing.event_type') }}</dt>
                    <dd class="mt-1 font-mono text-sm text-[var(--ui-text)]">{{ $event->event_type }}</dd>
                </div>
                <div>
                    <dt class="ui-label">{{ __('common.status') }}</dt>
                    <dd class="mt-1"><x-ui.badge>{{ $event->status->value }}</x-ui.badge></dd>
                </div>
                <div>
                    <dt class="ui-label">{{ __('common.tenant') }}</dt>
                    <dd class="mt-1 text-[var(--ui-text)]">{{ $event->tenant?->name ?? __('common.unassigned') }}</dd>
                </div>
                <div>
                    <dt class="ui-label">{{ __('admin.billing.subscription') }}</dt>
                    <dd class="mt-1 font-mono text-sm text-[var(--ui-text)]">{{ $event->subscription_id ?? __('common.not_set') }}</dd>
                </div>
                <div>
                    <dt class="ui-label">{{ __('common.created_at') }}</dt>
                    <dd class="mt-1 text-[var(--ui-text)]">{{ $event->created_at?->format('Y-m-d H:i') ?? __('common.not_set') }}</dd>
                </div>
                <div>
                    <dt class="ui-label">{{ __('admin.billing.processed_at') }}</dt>
                    <dd class="mt-1 text-[var(--ui-text)]">{{ $event->processed_at?->format('Y-m-d H:i') ?? __('common.not_set') }}</dd>
                </div>
                <div>
                    <dt class="ui-label">{{ __('admin.billing.failed_at') }}</dt>
                    <dd class="mt-1 text-[var(--ui-text)]">{{ $event->failed_at?->format('Y-m-d H:i') ?? __('common.not_set') }}</dd>
                </div>
            </dl>
        </div>

        <aside class="ui-card">
            <div class="ui-card-header">
                <h2 class="ui-heading-2">{{ __('admin.billing.failure_reason') }}</h2>
            </div>
            <div class="ui-card-body">
                <p class="break-words text-sm text-[var(--ui-text)]">{{ $event->failure_reason ?? __('common.not_set') }}</p>
            </div>
        </aside>
    </section>

    <section class="ui-card mt-6">
        <div class="ui-card-header">
            <h2 class="ui-heading-2">{{ __('admin.billing.payload') }}</h2>
        </div>
        <div class="ui-card-body">
            <pre class="overflow-x-auto rounded border border-[var(--ui-border)] bg-[var(--ui-surface-muted)] p-4 text-xs text-[var(--ui-text)]">{{ $payload }}</pre>
        </div>
    </section>
@endsection
