@extends('admin.layout')

@section('title', __('common.dashboard').' | '.__('app.admin_title'))
@section('heading', __('admin.admin_console'))
@section('subheading', __('admin.system_controls'))

@section('content')
    <section class="ui-card p-5">
        <h2 class="ui-heading-2">{{ __('admin.dashboard_title') }}</h2>
        <p class="ui-body mt-2">
            {{ __('admin.dashboard_description') }}
        </p>
    </section>
@endsection
