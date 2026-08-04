@extends('admin.layout')

@section('title', $title.' | '.__('app.admin_title'))
@section('heading', $title)
@section('subheading', $description)

@section('content')
    <section class="ui-card p-5">
        <h2 class="ui-heading-2">{{ $title }}</h2>
        <p class="ui-body mt-2">{{ $description }}</p>
    </section>
@endsection
