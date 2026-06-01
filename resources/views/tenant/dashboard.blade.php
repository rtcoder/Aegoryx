@extends('tenant.layout')

@section('title', $tenant->name.' | Aegoryx Tenant Panel')
@section('heading', 'Dashboard')
@section('subheading', 'Workspace overview for '.$tenant->name.'.')

@section('content')
    <div class="grid gap-5 xl:grid-cols-[1fr_360px]">
        <section class="rounded border border-neutral-800 bg-neutral-900 p-5">
            <h2 class="text-lg font-semibold">Tenant panel</h2>
            <p class="mt-2 text-sm leading-6 text-neutral-400">
                This shell is ready for tenant modules while keeping landlord-only details out of the workspace.
            </p>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded border border-neutral-800 bg-neutral-950 p-4">
                    <p class="text-xs uppercase tracking-wide text-neutral-500">CMS</p>
                    <p class="mt-2 text-sm text-neutral-300">Publishing module placeholder.</p>
                </div>
                <div class="rounded border border-neutral-800 bg-neutral-950 p-4">
                    <p class="text-xs uppercase tracking-wide text-neutral-500">CRM</p>
                    <p class="mt-2 text-sm text-neutral-300">Customer work module placeholder.</p>
                </div>
                <div class="rounded border border-neutral-800 bg-neutral-950 p-4">
                    <p class="text-xs uppercase tracking-wide text-neutral-500">Files</p>
                    <p class="mt-2 text-sm text-neutral-300">Private file access placeholder.</p>
                </div>
                <div class="rounded border border-neutral-800 bg-neutral-950 p-4">
                    <p class="text-xs uppercase tracking-wide text-neutral-500">Settings</p>
                    <p class="mt-2 text-sm text-neutral-300">Workspace configuration placeholder.</p>
                </div>
            </div>
        </section>

        <aside class="rounded border border-neutral-800 bg-neutral-900 p-5">
            <h2 class="text-lg font-semibold">Tenant context</h2>
            <dl class="mt-5 space-y-4">
                <div>
                    <dt class="text-xs uppercase tracking-wide text-neutral-500">Tenant</dt>
                    <dd class="mt-1 text-neutral-100">{{ $tenant->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-neutral-500">Slug</dt>
                    <dd class="mt-1 font-mono text-sm text-neutral-100">{{ $tenant->slug }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-neutral-500">Status</dt>
                    <dd class="mt-1 text-neutral-100">{{ $tenant->status->value }}</dd>
                </div>
            </dl>
        </aside>
    </div>
@endsection
