@extends('tenant.layout')

@section('title', __('crm.tasks').' | '.$tenant->name.' | '.__('app.tenant_panel_title'))
@section('heading', __('crm.tasks'))
@section('subheading', __('crm.tasks_description'))

@section('content')
    @include('tenant.crm.partials.navigation')

    <div class="grid gap-5 xl:grid-cols-[360px_1fr]">
        <x-ui.card :title="__('crm.create_task')">
            <form method="POST" action="{{ route('tenant.crm.tasks.store') }}" class="mt-5 space-y-4">
                @csrf

                @include('tenant.crm.tasks.partials.fields')

                <x-ui.button type="submit" class="w-full">
                    {{ __('crm.create_task') }}
                </x-ui.button>
            </form>
        </x-ui.card>

        <section class="ui-card">
            <div class="ui-card-header">
                <h2 class="ui-heading-2">{{ __('crm.task_list') }}</h2>
                <p class="ui-body mt-1">{{ __('crm.tasks_description') }}</p>
            </div>

            <div class="overflow-x-auto">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th>{{ __('crm.task_title') }}</th>
                            <th>{{ __('crm.subject') }}</th>
                            <th>{{ __('common.status') }}</th>
                            <th>{{ __('crm.due_date') }}</th>
                            <th>{{ __('crm.assigned_to') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tasks as $task)
                            @php
                                $badgeVariant = match ($task->status) {
                                    \App\Modules\Crm\Enums\CrmTaskStatus::Completed => 'success',
                                    \App\Modules\Crm\Enums\CrmTaskStatus::Cancelled => 'danger',
                                    default => 'accent',
                                };
                            @endphp
                            <tr>
                                <td class="font-medium text-[var(--ui-text)]">{{ $task->title }}</td>
                                <td>
                                    <x-ui.badge>{{ __("crm.subject_type.{$task->subject_type->value}") }}</x-ui.badge>
                                    <span class="ml-2 text-[var(--ui-text-muted)]">{{ $task->subjectLabel() }}</span>
                                </td>
                                <td>
                                    <x-ui.badge :variant="$badgeVariant">
                                        {{ __("crm.task_status.{$task->status->value}") }}
                                    </x-ui.badge>
                                </td>
                                <td class="text-[var(--ui-text-muted)]">{{ $task->due_date?->toDateString() ?? __('common.not_set') }}</td>
                                <td class="text-[var(--ui-text-muted)]">{{ $task->assignee?->name ?? __('crm.unassigned') }}</td>
                                <td class="text-right">
                                    <div class="inline-flex items-center gap-3">
                                        <a href="{{ route('tenant.crm.tasks.edit', $task) }}" wire:navigate class="ui-link">
                                            {{ __('common.manage') }}
                                        </a>
                                        <form method="POST" action="{{ route('tenant.crm.tasks.destroy', $task) }}">
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
                                <td colspan="6" class="py-10 text-center text-[var(--ui-text-muted)]">{{ __('crm.no_tasks') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-[var(--ui-border)] px-5 py-4">
                {{ $tasks->links() }}
            </div>
        </section>
    </div>
@endsection
