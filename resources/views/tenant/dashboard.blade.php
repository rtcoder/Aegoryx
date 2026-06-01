@extends('tenant.layout')

@section('title', $tenant->name.' | '.__('app.tenant_panel_title'))
@section('heading', __('common.dashboard'))
@section('subheading', __('tenant_panel.workspace_overview_for', ['tenant' => $tenant->name]))

@section('content')
    <div class="grid gap-5 xl:grid-cols-[1fr_360px]">
        <section class="rounded border border-neutral-800 bg-neutral-900 p-5">
            <h2 class="text-lg font-semibold">{{ __('tenant_panel.dashboard_heading') }}</h2>
            <p class="mt-2 text-sm leading-6 text-neutral-400">
                {{ __('tenant_panel.dashboard_description') }}
            </p>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                @foreach ($tenantModuleCards as $module)
                    @if ($module['enabled'])
                        <a href="{{ route($module['route']) }}" wire:navigate class="rounded border border-neutral-800 bg-neutral-950 p-4 hover:border-neutral-600">
                            <p class="text-xs uppercase tracking-wide text-neutral-500">{{ $module['label'] }}</p>
                            <p class="mt-2 text-sm text-neutral-300">{{ $module['description'] }}</p>
                        </a>
                    @else
                        <div class="rounded border border-neutral-800 bg-neutral-950 p-4 opacity-60">
                            <p class="text-xs uppercase tracking-wide text-neutral-500">{{ $module['label'] }}</p>
                            <p class="mt-2 text-sm text-neutral-500">{{ __('tenant_panel.not_enabled') }}</p>
                        </div>
                    @endif
                @endforeach
            </div>
        </section>

        <aside class="rounded border border-neutral-800 bg-neutral-900 p-5">
            <h2 class="text-lg font-semibold">{{ __('tenant_panel.context') }}</h2>
            <dl class="mt-5 space-y-4">
                <div>
                    <dt class="text-xs uppercase tracking-wide text-neutral-500">{{ __('common.tenant') }}</dt>
                    <dd class="mt-1 text-neutral-100">{{ $tenant->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-neutral-500">{{ __('common.slug') }}</dt>
                    <dd class="mt-1 font-mono text-sm text-neutral-100">{{ $tenant->slug }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-neutral-500">{{ __('common.status') }}</dt>
                    <dd class="mt-1 text-neutral-100">{{ $tenant->status->value }}</dd>
                </div>
            </dl>
        </aside>
    </div>
@endsection
