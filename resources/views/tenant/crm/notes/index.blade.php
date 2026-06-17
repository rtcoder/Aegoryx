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
                <h2 class="ui-heading-2">{{ __('crm.note_list') }}</h2>
                <p class="ui-body mt-1">{{ __('crm.notes_description') }}</p>
            </div>

            <div class="overflow-x-auto">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th>{{ __('crm.subject') }}</th>
                            <th>{{ __('crm.note_body') }}</th>
                            <th>{{ __('common.created_at') }}</th>
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
                                <td colspan="4" class="py-10 text-center text-[var(--ui-text-muted)]">{{ __('crm.no_notes') }}</td>
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
