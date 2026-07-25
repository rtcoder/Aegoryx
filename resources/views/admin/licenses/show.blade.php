@extends('admin.layout')

@section('title', __('common.licenses').' '.$license->id.' | '.__('app.admin_title'))
@section('heading', __('common.licenses').' '.$license->id)
@section('subheading', __('admin.sections.license_show'))

@section('content')
    <livewire:admin.licenses.show :license="$license" />
@endsection
