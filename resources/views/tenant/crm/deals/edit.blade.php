@extends('tenant.layout')

@section('title', __('crm.edit_deal').' | '.$tenant->name.' | '.__('app.tenant_panel_title'))
@section('heading', __('crm.edit_deal'))
@section('subheading', $deal->title)

@section('content')
    <x-ui.card class="max-w-2xl">
        <form method="POST" action="{{ route('tenant.crm.deals.update', $deal) }}" class="space-y-4">
            @csrf
            @method('PATCH')

            @include('tenant.crm.deals.partials.fields', ['deal' => $deal])

            <div class="flex items-center gap-3">
                <x-ui.button type="submit">
                    {{ __('common.save') }}
                </x-ui.button>
                <x-ui.button :href="route('tenant.crm.deals.index')" wire:navigate variant="secondary">
                    {{ __('common.back') }}
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
@endsection
