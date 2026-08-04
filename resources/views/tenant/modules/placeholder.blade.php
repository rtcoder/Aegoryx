@extends('tenant.layout')

@section('title', $title.' | '.$tenant->name.' | '.__('app.tenant_panel_title'))
@section('heading', $title)
@section('subheading', $description)

@section('content')
    <section class="ui-card p-5">
        <h2 class="ui-heading-2">{{ $title }}</h2>
        <p class="ui-body mt-2">{{ $description }}</p>
        <div class="ui-muted-panel mt-6 p-4">
            <p class="ui-body">{{ __('tenant_panel.module_placeholder') }}</p>
        </div>
    </section>
@endsection
