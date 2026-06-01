<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@php
    $tenantNavigation = $tenantNavigation ?? [];
    $tenantModuleCards = $tenantModuleCards ?? [];
    $tenantEntitlements = $tenantEntitlements ?? [];
@endphp
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', __('app.tenant_panel_title'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-neutral-950 text-neutral-100 antialiased">
    <div class="mx-auto flex min-h-screen w-full max-w-7xl">
        <aside class="hidden w-64 shrink-0 border-r border-neutral-800 px-5 py-6 lg:block">
            <div>
                <p class="text-lg font-semibold">Aegoryx</p>
                <p class="mt-1 text-xs uppercase tracking-wide text-neutral-500">{{ __('tenant_panel.label') }}</p>
            </div>

            <div class="mt-6 rounded border border-neutral-800 bg-neutral-900 p-4">
                <p class="text-xs uppercase tracking-wide text-neutral-500">{{ __('tenant_panel.active_tenant') }}</p>
                <p class="mt-2 font-medium text-neutral-100">{{ $tenant->name }}</p>
                <p class="mt-1 font-mono text-xs text-neutral-500">{{ $tenant->slug }}</p>
            </div>

            <nav class="mt-8 space-y-1" aria-label="{{ __('tenant_panel.label') }}">
                @foreach ($tenantNavigation as $item)
                    <a
                        href="{{ route($item['route']) }}"
                        wire:navigate
                        class="block rounded px-3 py-2 text-sm {{ request()->routeIs($item['route']) ? 'bg-sky-500 text-white' : 'text-neutral-300 hover:bg-neutral-900 hover:text-white' }}"
                    >
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="mt-8">
                <p class="px-3 text-xs uppercase tracking-wide text-neutral-500">{{ __('tenant_panel.modules') }}</p>
                <div class="mt-3 space-y-2">
                    @foreach ($tenantModuleCards as $module)
                        @if ($module['enabled'])
                            <a href="{{ route($module['route']) }}" wire:navigate class="block rounded border border-neutral-800 px-3 py-2 hover:border-neutral-600">
                                <p class="text-sm font-medium text-neutral-300">{{ $module['label'] }}</p>
                                <p class="mt-1 text-xs text-neutral-500">{{ $module['description'] }}</p>
                            </a>
                        @else
                            <div class="rounded border border-neutral-800 px-3 py-2 opacity-60">
                                <p class="text-sm font-medium text-neutral-400">{{ $module['label'] }}</p>
                                <p class="mt-1 text-xs text-neutral-600">{{ __('tenant_panel.not_enabled') }}</p>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="border-b border-neutral-800 px-5 py-4 md:px-8">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-neutral-500">{{ __('tenant_panel.label') }}</p>
                        <h1 class="mt-1 text-xl font-semibold">@yield('heading', __('common.dashboard'))</h1>
                        <p class="mt-1 text-sm text-neutral-400">@yield('subheading', __('tenant_panel.workspace_for', ['tenant' => $tenant->name]))</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="rounded border border-neutral-800 bg-neutral-900 px-4 py-2 text-sm">
                            <p class="text-neutral-300">{{ auth()->user()?->name ?? __('common.tenant_user') }}</p>
                            <p class="mt-1 text-xs text-neutral-500">{{ auth()->user()?->email }}</p>
                        </div>
                        <form method="POST" action="{{ route('tenant.logout') }}">
                            @csrf
                            <button type="submit" class="rounded border border-neutral-700 px-4 py-2 text-sm text-neutral-200 hover:border-neutral-500">
                                {{ __('tenant_panel.sign_out') }}
                            </button>
                        </form>
                    </div>
                </div>

                <div class="mt-5 flex gap-2 overflow-x-auto lg:hidden">
                    <a
                        href="{{ route('tenant.dashboard') }}"
                        wire:navigate
                        class="shrink-0 rounded bg-sky-500 px-3 py-2 text-sm text-white"
                    >
                        {{ __('common.dashboard') }}
                    </a>
                    @foreach ($tenantModuleCards as $module)
                        @if ($module['enabled'])
                            <a href="{{ route($module['route']) }}" wire:navigate class="shrink-0 rounded bg-neutral-900 px-3 py-2 text-sm text-neutral-300">
                                {{ $module['label'] }}
                            </a>
                        @else
                            <span class="shrink-0 rounded bg-neutral-900 px-3 py-2 text-sm text-neutral-600">
                                {{ $module['label'] }}
                            </span>
                        @endif
                    @endforeach
                </div>
            </header>

            <main class="flex-1 px-5 py-6 md:px-8">
                <div class="mb-5 rounded border border-neutral-800 bg-neutral-900 px-4 py-3">
                    <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm">
                        <span class="text-neutral-500">{{ __('tenant_panel.active_tenant') }}</span>
                        <span class="font-medium text-neutral-100">{{ $tenant->name }}</span>
                        <span class="font-mono text-xs text-neutral-500">{{ $tenant->slug }}</span>
                        <span class="text-neutral-400">{{ $tenant->status->value }}</span>
                        <span class="text-neutral-500">{{ __('tenant_panel.enabled_features', ['count' => collect($tenantEntitlements)->where('enabled', true)->count()]) }}</span>
                    </div>
                </div>

                @yield('content')
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
