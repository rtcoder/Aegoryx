@extends('tenant.layout')

@section('title', $tenant->name.' | '.__('app.tenant_panel_title'))
@section('heading', __('common.dashboard'))
@section('subheading', __('tenant_panel.workspace_overview_for', ['tenant' => $tenant->name]))

@section('content')
    <div class="grid gap-5 xl:grid-cols-[1fr_360px]">
        <section class="ui-card p-5">
            <h2 class="ui-heading-2">{{ __('tenant_panel.dashboard_heading') }}</h2>
            <p class="ui-body mt-2">
                {{ __('tenant_panel.dashboard_description') }}
            </p>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                @foreach ($tenantModuleCards as $module)
                    @if ($module['enabled'])
                        <a href="{{ route($module['route']) }}" wire:navigate class="ui-muted-panel block p-4 hover:border-[var(--ui-border-strong)]">
                            <p class="ui-caption uppercase tracking-wide">{{ $module['label'] }}</p>
                            <p class="ui-body mt-2">{{ $module['description'] }}</p>
                        </a>
                    @else
                        <div class="ui-muted-panel p-4 opacity-60">
                            <p class="ui-caption uppercase tracking-wide">{{ $module['label'] }}</p>
                            <p class="ui-caption mt-2">{{ __('tenant_panel.not_enabled') }}</p>
                        </div>
                    @endif
                @endforeach
            </div>
        </section>

        <aside class="ui-card p-5">
            <h2 class="ui-heading-2">{{ __('tenant_panel.context') }}</h2>
            <dl class="mt-5 space-y-4">
                <div>
                    <dt class="ui-caption uppercase tracking-wide">{{ __('common.tenant') }}</dt>
                    <dd class="mt-1 text-[var(--ui-text)]">{{ $tenant->name }}</dd>
                </div>
                <div>
                    <dt class="ui-caption uppercase tracking-wide">{{ __('common.slug') }}</dt>
                    <dd class="mt-1 font-mono text-sm text-[var(--ui-text)]">{{ $tenant->slug }}</dd>
                </div>
                <div>
                    <dt class="ui-caption uppercase tracking-wide">{{ __('common.status') }}</dt>
                    <dd class="mt-1 text-[var(--ui-text)]">{{ $tenant->status->value }}</dd>
                </div>
            </dl>
        </aside>
    </div>
@endsection
