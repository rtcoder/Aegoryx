@extends('landlord.layout')

@section('title', 'Aegoryx Admin')
@section('heading', 'Dashboard')
@section('subheading', 'System overview for the landlord console.')

@section('content')
    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <a href="{{ route('landlord.tenants.index') }}" class="rounded border border-neutral-800 bg-neutral-900 p-5 hover:border-neutral-600">
            <p class="text-sm text-neutral-400">Tenants</p>
            <h2 class="mt-2 text-lg font-semibold">Tenant management</h2>
        </a>

        <a href="{{ route('landlord.features.index') }}" class="rounded border border-neutral-800 bg-neutral-900 p-5 hover:border-neutral-600">
            <p class="text-sm text-neutral-400">Features</p>
            <h2 class="mt-2 text-lg font-semibold">Feature access</h2>
        </a>

        <a href="{{ route('landlord.support.index') }}" class="rounded border border-neutral-800 bg-neutral-900 p-5 hover:border-neutral-600">
            <p class="text-sm text-neutral-400">Support</p>
            <h2 class="mt-2 text-lg font-semibold">Audited access</h2>
        </a>
    </section>
@endsection
