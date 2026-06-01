@extends('landlord.layout')

@section('title', 'Features | Aegoryx Admin')
@section('heading', 'Features')
@section('subheading', 'Manage global features and tenant-specific feature overrides.')

@section('content')
    <livewire:landlord.features.index />
@endsection
