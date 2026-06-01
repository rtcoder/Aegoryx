@extends('tenant.layout')

@section('title', $title.' | '.$tenant->name.' | '.__('app.tenant_panel_title'))
@section('heading', $title)
@section('subheading', $description)

@section('content')
    <section class="rounded border border-neutral-800 bg-neutral-900 p-5">
        <h2 class="text-lg font-semibold">{{ $title }}</h2>
        <p class="mt-2 text-sm leading-6 text-neutral-400">{{ $description }}</p>
        <div class="mt-6 rounded border border-neutral-800 bg-neutral-950 p-4">
            <p class="text-sm text-neutral-300">{{ __('tenant_panel.module_placeholder') }}</p>
        </div>
    </section>
@endsection
