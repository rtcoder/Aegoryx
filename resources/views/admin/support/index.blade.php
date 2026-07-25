@extends('admin.layout')

@section('title', __('common.support').' | '.__('app.admin_title'))
@section('heading', __('common.support'))
@section('subheading', __('admin.sections.support'))

@section('content')
    <livewire:admin.support.index />
@endsection
