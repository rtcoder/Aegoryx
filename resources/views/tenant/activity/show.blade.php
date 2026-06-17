@extends('tenant.layout')

@php
    $before = json_encode($entry->before_json ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    $after = json_encode($entry->after_json ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    $metadata = json_encode($entry->metadata_json ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
@endphp

@section('title', __('audit_view.activity_entry_title'))
@section('heading', __('audit_view.activity_entry_title'))
@section('subheading', __('audit_view.activity_entry_description'))

@section('content')
    <div class="mb-6">
        <x-ui.button :href="route('tenant.activity.index')" variant="secondary">
            {{ __('common.back') }}
        </x-ui.button>
    </div>

    <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_minmax(320px,420px)]">
        <div class="ui-card">
            <div class="ui-card-header">
                <h2 class="ui-heading-2">{{ __('audit_view.event') }}</h2>
                <p class="ui-body mt-1">{{ $entry->description ?? __('common.not_set') }}</p>
            </div>

            <dl class="ui-card-body grid gap-4 sm:grid-cols-2">
                <div>
                    <dt class="ui-label">{{ __('audit_view.action') }}</dt>
                    <dd class="mt-1"><x-ui.badge>{{ $entry->action->value }}</x-ui.badge></dd>
                </div>
                <div>
                    <dt class="ui-label">{{ __('audit_view.created_at') }}</dt>
                    <dd class="mt-1 text-[var(--ui-text)]">{{ $entry->created_at?->format('Y-m-d H:i') ?? __('common.not_set') }}</dd>
                </div>
                <div>
                    <dt class="ui-label">{{ __('audit_view.actor') }}</dt>
                    <dd class="mt-1 font-mono text-sm text-[var(--ui-text)]">{{ class_basename($entry->actor_type) }} #{{ $entry->actor_id ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="ui-label">{{ __('audit_view.subject') }}</dt>
                    <dd class="mt-1 font-mono text-sm text-[var(--ui-text)]">{{ class_basename($entry->subject_type) }} #{{ $entry->subject_id ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="ui-label">{{ __('audit_view.ip') }}</dt>
                    <dd class="mt-1 font-mono text-sm text-[var(--ui-text)]">{{ $entry->ip ?? __('common.not_set') }}</dd>
                </div>
                <div>
                    <dt class="ui-label">{{ __('audit_view.user_agent') }}</dt>
                    <dd class="mt-1 break-words text-sm text-[var(--ui-text)]">{{ $entry->user_agent ?? __('common.not_set') }}</dd>
                </div>
            </dl>
        </div>

        <aside class="ui-card">
            <div class="ui-card-header">
                <h2 class="ui-heading-2">{{ __('audit_view.metadata') }}</h2>
            </div>
            <div class="ui-card-body">
                <pre class="overflow-x-auto rounded border border-[var(--ui-border)] bg-[var(--ui-surface-muted)] p-4 text-xs text-[var(--ui-text)]">{{ $metadata }}</pre>
            </div>
        </aside>
    </section>

    <section class="mt-6 grid gap-6 xl:grid-cols-2">
        <div class="ui-card">
            <div class="ui-card-header">
                <h2 class="ui-heading-2">{{ __('audit_view.before') }}</h2>
            </div>
            <div class="ui-card-body">
                <pre class="overflow-x-auto rounded border border-[var(--ui-border)] bg-[var(--ui-surface-muted)] p-4 text-xs text-[var(--ui-text)]">{{ $before }}</pre>
            </div>
        </div>

        <div class="ui-card">
            <div class="ui-card-header">
                <h2 class="ui-heading-2">{{ __('audit_view.after') }}</h2>
            </div>
            <div class="ui-card-body">
                <pre class="overflow-x-auto rounded border border-[var(--ui-border)] bg-[var(--ui-surface-muted)] p-4 text-xs text-[var(--ui-text)]">{{ $after }}</pre>
            </div>
        </div>
    </section>
@endsection
