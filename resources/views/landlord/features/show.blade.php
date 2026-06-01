@extends('landlord.layout')

@section('title', $feature->name.' | '.__('app.admin_title'))
@section('heading', $feature->name)
@section('subheading', __('landlord.sections.feature_show'))

@section('content')
    <livewire:landlord.features.show :feature="$feature" />
@endsection
