@extends('tenant.layout')

@section('title', __('tenant_panel.users.title'))
@section('heading', __('tenant_panel.users.title'))
@section('subheading', __('tenant_panel.users.description'))

@section('content')
    <livewire:tenant.users.index />
@endsection
