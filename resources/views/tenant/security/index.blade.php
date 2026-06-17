@extends('tenant.layout')

@section('title', __('two_factor.security_title'))
@section('heading', __('two_factor.security_title'))
@section('subheading', __('two_factor.tenant_security_description'))

@section('content')
    <livewire:tenant.security.two-factor-settings />
@endsection
