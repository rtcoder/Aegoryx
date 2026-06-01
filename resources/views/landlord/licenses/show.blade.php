@extends('landlord.layout')

@section('title', 'License '.$license->id.' | Aegoryx Admin')
@section('heading', 'License '.$license->id)
@section('subheading', 'Effective license state and verification controls.')

@section('content')
    <livewire:landlord.licenses.show :license="$license" />
@endsection
