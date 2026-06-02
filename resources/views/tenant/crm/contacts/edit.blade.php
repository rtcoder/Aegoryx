@extends('tenant.layout')

@section('title', __('crm.edit_contact').' | '.$tenant->name.' | '.__('app.tenant_panel_title'))
@section('heading', __('crm.edit_contact'))
@section('subheading', trim($contact->first_name.' '.$contact->last_name))

@section('content')
    <section class="max-w-2xl rounded border border-neutral-800 bg-neutral-900 p-5">
        <form method="POST" action="{{ route('tenant.crm.contacts.update', $contact) }}" class="space-y-4">
            @csrf
            @method('PATCH')

            @include('tenant.crm.contacts.partials.fields', ['contact' => $contact])

            <div class="flex items-center gap-3">
                <button type="submit" class="rounded bg-sky-500 px-4 py-2 font-medium text-white hover:bg-sky-400">
                    {{ __('common.save') }}
                </button>
                <a href="{{ route('tenant.crm.index') }}" wire:navigate class="rounded border border-neutral-700 px-4 py-2 text-sm text-neutral-200 hover:border-neutral-500">
                    {{ __('common.back') }}
                </a>
            </div>
        </form>
    </section>
@endsection
