@extends('tenant.layout')

@section('title', __('crm.edit_company').' | '.$tenant->name.' | '.__('app.tenant_panel_title'))
@section('heading', __('crm.edit_company'))
@section('subheading', $company->name)

@section('content')
    <x-ui.card class="max-w-2xl">
        <form method="POST" action="{{ route('tenant.crm.companies.update', $company) }}" class="space-y-4">
            @csrf
            @method('PATCH')

            @include('tenant.crm.companies.partials.fields', ['company' => $company])

            <div class="flex items-center gap-3">
                <x-ui.button type="submit">
                    {{ __('common.save') }}
                </x-ui.button>
                <x-ui.button :href="route('tenant.crm.companies.index')" wire:navigate variant="secondary">
                    {{ __('common.back') }}
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
@endsection
