@extends('landlord.layout')

@section('title', 'Licenses | Aegoryx Admin')
@section('heading', 'Licenses')
@section('subheading', 'Review license state, verification status, and self-hosted access.')

@section('content')
    <livewire:landlord.licenses.index />
@endsection
