@extends('landlord.layout')

@section('title', $tenant->name.' | Aegoryx Admin')
@section('heading', $tenant->name)
@section('subheading', 'Tenant details and operational controls.')

@section('content')
    <livewire:landlord.tenants.show :tenant="$tenant" />
@endsection
