@extends('tenant.layout')

@section('title', __('crm.edit_contact').' | '.$tenant->name.' | '.__('app.tenant_panel_title'))
@section('heading', __('crm.edit_contact'))
@section('subheading', trim($contact->first_name.' '.$contact->last_name))

@section('content')
    <x-ui.card class="max-w-2xl">
        <form method="POST" action="{{ route('tenant.crm.contacts.update', $contact) }}" class="space-y-4">
            @csrf
            @method('PATCH')

            @include('tenant.crm.contacts.partials.fields', ['contact' => $contact])

            <div class="flex items-center gap-3">
                <x-ui.button type="submit">
                    {{ __('common.save') }}
                </x-ui.button>
                <x-ui.button :href="route('tenant.crm.index')" wire:navigate variant="secondary">
                    {{ __('common.back') }}
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
@endsection
