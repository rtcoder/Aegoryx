@extends('landlord.layout')

@section('title', __('common.dashboard').' | '.__('app.admin_title'))
@section('heading', __('landlord.admin_console'))
@section('subheading', __('landlord.system_controls'))

@section('content')
    <section class="rounded border border-neutral-800 bg-neutral-900 p-5">
        <h2 class="text-lg font-semibold">{{ __('landlord.dashboard_title') }}</h2>
        <p class="mt-2 text-sm leading-6 text-neutral-400">
            {{ __('landlord.dashboard_description') }}
        </p>
    </section>
@endsection
