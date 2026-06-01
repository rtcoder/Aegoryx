@extends('landlord.layout')

@section('title', $tenant->name.' | '.__('app.admin_title'))
@section('heading', $tenant->name)
@section('subheading', __('landlord.sections.tenant_show'))

@section('content')
    <livewire:landlord.tenants.show :tenant="$tenant" />
@endsection
