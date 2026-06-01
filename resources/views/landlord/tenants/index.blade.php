@extends('landlord.layout')

@section('title', __('common.tenants').' | '.__('app.admin_title'))
@section('heading', __('common.tenants'))
@section('subheading', __('landlord.sections.tenants'))

@section('content')
    <livewire:landlord.tenants.index />
@endsection
