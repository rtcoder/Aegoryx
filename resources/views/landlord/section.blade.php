@extends('landlord.layout')

@section('title', $title.' | Aegoryx Admin')
@section('heading', $title)
@section('subheading', $description)

@section('content')
    <section class="rounded border border-neutral-800 bg-neutral-900 p-5">
        <h2 class="text-lg font-semibold">{{ $title }}</h2>
        <p class="mt-2 text-sm leading-6 text-neutral-400">{{ $description }}</p>
    </section>
@endsection
