@extends('tenant.layout')

@section('title', __('crm.notes').' | '.$tenant->name.' | '.__('app.tenant_panel_title'))
@section('heading', __('crm.notes'))
@section('subheading', __('crm.notes_description'))

@section('content')
    @include('tenant.crm.partials.navigation')

    <div class="grid gap-5 xl:grid-cols-[360px_1fr]">
        <x-ui.card :title="__('crm.create_note')">
            <form method="POST" action="{{ route('tenant.crm.notes.store') }}" class="mt-5 space-y-4">
                @csrf

                @include('tenant.crm.notes.partials.fields')

                <x-ui.button type="submit" class="w-full">
                    {{ __('crm.create_note') }}
                </x-ui.button>
            </form>
        </x-ui.card>

        <section class="ui-card">
            <div class="ui-card-header">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <h2 class="ui-heading-2">{{ __('crm.note_list') }}</h2>
                        <p class="ui-body mt-1">{{ __('crm.notes_description') }}</p>
                    </div>
                    <form method="GET" action="{{ route('tenant.crm.notes.index') }}" class="flex flex-col gap-2 sm:flex-row">
                        <input name="search" value="{{ $search }}" class="ui-input min-w-64" placeholder="{{ __('common.search_placeholder') }}">
                        <x-ui.button type="submit" variant="secondary">{{ __('common.search') }}</x-ui.button>
                        @if ($search !== '')
                            <x-ui.button :href="route('tenant.crm.notes.index')" variant="ghost">{{ __('common.clear_search') }}</x-ui.button>
                        @endif
                    </form>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th>{{ __('crm.subject') }}</th>
                            <th><x-table.sort-link sort="body" :current-sort="$sort" :current-direction="$direction">{{ __('crm.note_body') }}</x-table.sort-link></th>
                            <th><x-table.sort-link sort="created_at" :current-sort="$sort" :current-direction="$direction">{{ __('common.created_at') }}</x-table.sort-link></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($notes as $note)
                            <tr>
                                <td>
                                    <x-ui.badge>{{ __("crm.subject_type.{$note->subject_type->value}") }}</x-ui.badge>
                                    <span class="ml-2 text-[var(--ui-text-muted)]">{{ $note->subjectLabel() }}</span>
                                </td>
                                <td class="max-w-xl text-[var(--ui-text-muted)]">{{ str($note->body)->limit(120) }}</td>
                                <td class="text-[var(--ui-text-muted)]">{{ $note->created_at?->format('Y-m-d') }}</td>
                                <td class="text-right">
                                    <div class="inline-flex items-center gap-3">
                                        <a href="{{ route('tenant.crm.notes.edit', $note) }}" wire:navigate class="ui-link">
                                            {{ __('common.manage') }}
                                        </a>
                                        <form method="POST" action="{{ route('tenant.crm.notes.destroy', $note) }}">
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
                                <td colspan="4" class="py-10 text-center text-[var(--ui-text-muted)]">{{ $search === '' ? __('crm.no_notes') : __('common.no_results') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-[var(--ui-border)] px-5 py-4">
                {{ $notes->links() }}
            </div>
        </section>
    </div>
@endsection
