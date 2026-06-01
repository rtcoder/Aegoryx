@extends('landlord.layout')

@section('title', __('common.licenses').' '.$license->id.' | '.__('app.admin_title'))
@section('heading', __('common.licenses').' '.$license->id)
@section('subheading', __('landlord.sections.license_show'))

@section('content')
    <livewire:landlord.licenses.show :license="$license" />
@endsection
