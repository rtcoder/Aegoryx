@extends('tenant.layout')

@section('title', __('crm.edit_note').' | '.$tenant->name.' | '.__('app.tenant_panel_title'))
@section('heading', __('crm.edit_note'))
@section('subheading', __('crm.notes_description'))

@section('content')
    @include('tenant.crm.partials.navigation')

    <x-ui.card :title="__('crm.edit_note')" class="max-w-2xl">
        <form method="POST" action="{{ route('tenant.crm.notes.update', $note) }}" class="mt-5 space-y-4">
            @csrf
            @method('PATCH')

            @include('tenant.crm.notes.partials.fields')

            <div class="flex flex-wrap items-center gap-3">
                <x-ui.button type="submit">
                    {{ __('common.save_changes') }}
                </x-ui.button>
                <x-ui.button :href="route('tenant.crm.notes.index')" wire:navigate variant="secondary">
                    {{ __('common.cancel') }}
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
@endsection
