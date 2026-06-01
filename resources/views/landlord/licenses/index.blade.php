@extends('landlord.layout')

@section('title', __('common.licenses').' | '.__('app.admin_title'))
@section('heading', __('common.licenses'))
@section('subheading', __('landlord.sections.licenses'))

@section('content')
    <livewire:landlord.licenses.index />
@endsection
