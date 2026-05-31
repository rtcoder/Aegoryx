@extends('landlord.layout')

@section('title', $title.' | Aegoryx Admin')
@section('heading', $title)
@section('subheading', $description)

@section('content')
    <section class="rounded border border-neutral-800 bg-neutral-900 p-6">
        <p class="text-sm uppercase tracking-wide text-sky-300">Next task</p>
        <h2 class="mt-3 text-2xl font-semibold">{{ $title }}</h2>
        <p class="mt-3 max-w-2xl text-sm leading-6 text-neutral-400">{{ $description }}</p>
    </section>
@endsection
