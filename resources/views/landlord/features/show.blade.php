@extends('landlord.layout')

@section('title', $feature->name.' | Aegoryx Admin')
@section('heading', $feature->name)
@section('subheading', 'Feature registry details and manual tenant overrides.')

@section('content')
    <livewire:landlord.features.show :feature="$feature" />
@endsection
