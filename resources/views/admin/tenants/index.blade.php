@extends('admin.layout')

@section('title', __('common.tenants').' | '.__('app.admin_title'))
@section('heading', __('common.tenants'))
@section('subheading', __('admin.sections.tenants'))

@section('content')
    <livewire:admin.tenants.index />
@endsection
