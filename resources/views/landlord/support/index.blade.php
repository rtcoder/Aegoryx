@extends('landlord.layout')

@section('title', __('common.support').' | '.__('app.admin_title'))
@section('heading', __('common.support'))
@section('subheading', __('landlord.sections.support'))

@section('content')
    <livewire:landlord.support.index />
@endsection
