@extends('admin.layout')

@section('title', __('common.licenses').' | '.__('app.admin_title'))
@section('heading', __('common.licenses'))
@section('subheading', __('admin.sections.licenses'))

@section('content')
    <livewire:admin.licenses.index />
@endsection
