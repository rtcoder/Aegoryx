@extends('admin.layout')

@section('title', __('two_factor.security_title').' | '.__('app.admin_title'))
@section('heading', __('two_factor.security_title'))
@section('subheading', __('two_factor.security_description'))

@section('content')
    <livewire:admin.security.two-factor-settings />
@endsection
