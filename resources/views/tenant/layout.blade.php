@php
    $currentTheme = auth('web')->user()?->theme?->value ?? 'light';
    $tenantNavigation = $tenantNavigation ?? [];
    $tenantModuleCards = $tenantModuleCards ?? [];
    $tenantEntitlements = $tenantEntitlements ?? [];
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ $currentTheme }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('app.tenant_panel_title'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="ds-app antialiased">
    <div class="ds-shell flex">
        <aside class="ui-sidebar ui-shell-border hidden w-64 shrink-0 border-r px-5 py-6 lg:block">
            <div>
                <p class="text-lg font-semibold">Aegoryx</p>
                <p class="ui-caption mt-1 uppercase tracking-wide">{{ __('tenant_panel.label') }}</p>
            </div>

            <div class="ui-muted-panel mt-6 p-4">
                <p class="ui-caption uppercase tracking-wide">{{ __('tenant_panel.active_tenant') }}</p>
                <p class="mt-2 font-medium text-[var(--ui-text)]">{{ $tenant->name }}</p>
                <p class="ui-caption mt-1 font-mono">{{ $tenant->slug }}</p>
            </div>

            <nav class="mt-8 space-y-1" aria-label="{{ __('tenant_panel.label') }}">
                @foreach ($tenantNavigation as $item)
                    <a
                        href="{{ route($item['route']) }}"
                        wire:navigate
                        class="ui-nav-link block rounded px-3 py-2 text-sm {{ request()->routeIs($item['route']) ? 'ui-nav-link-active' : '' }}"
                    >
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="mt-8">
                <p class="ui-caption px-3 uppercase tracking-wide">{{ __('tenant_panel.modules') }}</p>
                <div class="mt-3 space-y-2">
                    @foreach ($tenantModuleCards as $module)
                        @if ($module['enabled'])
                            <a href="{{ route($module['route']) }}" wire:navigate class="ui-muted-panel block px-3 py-2 hover:border-[var(--ui-border-strong)]">
                                <p class="text-sm font-medium text-[var(--ui-text)]">{{ $module['label'] }}</p>
                                <p class="ui-caption mt-1">{{ $module['description'] }}</p>
                            </a>
                        @else
                            <div class="ui-muted-panel px-3 py-2 opacity-60">
                                <p class="text-sm font-medium text-[var(--ui-text-muted)]">{{ $module['label'] }}</p>
                                <p class="ui-caption mt-1">{{ __('tenant_panel.not_enabled') }}</p>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="ui-header ui-shell-border border-b px-5 py-4 md:px-8">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="ui-caption uppercase tracking-wide">{{ __('tenant_panel.label') }}</p>
                        <h1 class="ui-heading-1 mt-1">@yield('heading', __('common.dashboard'))</h1>
                        <p class="ui-body mt-1">@yield('subheading', __('tenant_panel.workspace_for', ['tenant' => $tenant->name]))</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <x-theme.switcher :theme="$currentTheme" :action="route('tenant.theme.update')" />
                        <div class="ui-muted-panel px-4 py-2 text-sm">
                            <p class="text-[var(--ui-text)]">{{ auth()->user()?->name ?? __('common.tenant_user') }}</p>
                            <p class="ui-caption mt-1">{{ auth()->user()?->email }}</p>
                        </div>
                        <form method="POST" action="{{ route('tenant.logout') }}">
                            @csrf
                        <x-ui.button type="submit" variant="secondary" size="sm">
                            {{ __('tenant_panel.sign_out') }}
                        </x-ui.button>
                    </form>
                </div>
                </div>

                <div class="mt-5 flex gap-2 overflow-x-auto lg:hidden">
                    <a
                        href="{{ route('tenant.dashboard') }}"
                        wire:navigate
                        class="ui-nav-pill-active shrink-0 rounded px-3 py-2 text-sm"
                    >
                        {{ __('common.dashboard') }}
                    </a>
                    @foreach ($tenantModuleCards as $module)
                        @if ($module['enabled'])
                            <a href="{{ route($module['route']) }}" wire:navigate class="ui-nav-pill shrink-0 rounded px-3 py-2 text-sm">
                                {{ $module['label'] }}
                            </a>
                        @else
                            <span class="ui-nav-pill shrink-0 rounded px-3 py-2 text-sm text-[var(--ui-text-subtle)]">
                                {{ $module['label'] }}
                            </span>
                        @endif
                    @endforeach
                </div>
            </header>

            <main class="flex-1 px-5 py-6 md:px-8">
                <div class="ui-muted-panel mb-5 px-4 py-3">
                    <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm">
                        <span class="text-[var(--ui-text-subtle)]">{{ __('tenant_panel.active_tenant') }}</span>
                        <span class="font-medium text-[var(--ui-text)]">{{ $tenant->name }}</span>
                        <span class="font-mono text-xs text-[var(--ui-text-subtle)]">{{ $tenant->slug }}</span>
                        <span class="text-[var(--ui-text-muted)]">{{ $tenant->status->value }}</span>
                        <span class="text-[var(--ui-text-subtle)]">{{ __('tenant_panel.enabled_features', ['count' => collect($tenantEntitlements)->where('enabled', true)->count()]) }}</span>
                    </div>
                </div>

                @yield('content')
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
