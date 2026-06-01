@extends('landlord.layout')

@section('title', 'Tenants | Aegoryx Admin')
@section('heading', 'Tenants')
@section('subheading', 'Manage tenant accounts, domains, deployment state, and support entry points.')

@section('content')
    <livewire:landlord.tenants.index />
@endsection
