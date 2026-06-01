@extends('tenant.layout')

@section('title', $tenant->name.' | Aegoryx Tenant Panel')

@section('content')
    <section class="rounded border border-neutral-800 bg-neutral-900 p-5">
        <h1 class="text-xl font-semibold">Tenant panel</h1>
        <dl class="mt-5 grid gap-4 sm:grid-cols-2">
            <div>
                <dt class="text-xs uppercase tracking-wide text-neutral-500">Tenant</dt>
                <dd class="mt-1 text-neutral-100">{{ $tenant->name }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide text-neutral-500">Slug</dt>
                <dd class="mt-1 text-neutral-100">{{ $tenant->slug }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide text-neutral-500">Schema</dt>
                <dd class="mt-1 font-mono text-sm text-neutral-100">{{ $tenant->schema_name }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide text-neutral-500">Status</dt>
                <dd class="mt-1 text-neutral-100">{{ $tenant->status->value }}</dd>
            </div>
        </dl>
    </section>
@endsection
