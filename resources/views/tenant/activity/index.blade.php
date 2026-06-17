@extends('tenant.layout')

@section('title', __('audit_view.activity_title'))
@section('heading', __('audit_view.activity_title'))
@section('subheading', __('audit_view.activity_description'))

@section('content')
    <section class="ui-card">
        <div class="ui-card-header flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h2 class="ui-heading-2">{{ __('audit_view.activity_entries') }}</h2>
                <p class="ui-body mt-1">{{ __('audit_view.activity_help') }}</p>
            </div>

            <form method="GET" action="{{ route('tenant.activity.index') }}" class="flex gap-3">
                <input name="action" value="{{ $action }}" class="ui-input" placeholder="{{ __('audit_view.filter_action') }}">
                <x-ui.button type="submit" variant="secondary">{{ __('audit_view.filter') }}</x-ui.button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="ui-table">
                <thead>
                    <tr>
                        <th>{{ __('audit_view.created_at') }}</th>
                        <th>{{ __('audit_view.action') }}</th>
                        <th>{{ __('audit_view.description') }}</th>
                        <th>{{ __('audit_view.actor') }}</th>
                        <th>{{ __('audit_view.subject') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($entries as $entry)
                        <tr>
                            <td class="whitespace-nowrap text-[var(--ui-text-muted)]">{{ $entry->created_at?->format('Y-m-d H:i') }}</td>
                            <td><x-ui.badge>{{ $entry->action->value }}</x-ui.badge></td>
                            <td class="text-[var(--ui-text)]">{{ $entry->description ?? __('common.not_set') }}</td>
                            <td class="font-mono text-xs text-[var(--ui-text-muted)]">{{ class_basename($entry->actor_type) }} #{{ $entry->actor_id ?? '-' }}</td>
                            <td class="font-mono text-xs text-[var(--ui-text-muted)]">{{ class_basename($entry->subject_type) }} #{{ $entry->subject_id ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-[var(--ui-text-muted)]">{{ __('audit_view.no_entries') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-[var(--ui-border)] px-5 py-4">
            {{ $entries->links() }}
        </div>
    </section>
@endsection
