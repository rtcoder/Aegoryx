@extends('tenant.layout')

@section('title', __('cms.title'))
@section('heading', __('cms.title'))
@section('subheading', __('cms.description'))

@section('content')
    <livewire:tenant.cms.pages.index />
@endsection
