@extends('admin.layout')

@section('title', $tenant->name.' | '.__('app.admin_title'))
@section('heading', $tenant->name)
@section('subheading', __('admin.sections.tenant_show'))

@section('content')
    <livewire:admin.tenants.show :tenant="$tenant" />
@endsection
