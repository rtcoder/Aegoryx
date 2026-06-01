@extends('landlord.layout')

@section('title', __('common.features').' | '.__('app.admin_title'))
@section('heading', __('common.features'))
@section('subheading', __('landlord.sections.features'))

@section('content')
    <livewire:landlord.features.index />
@endsection
