<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@php
    $navigation = [
        ['label' => 'Dashboard', 'route' => 'landlord.dashboard'],
        ['label' => 'Tenants', 'route' => 'landlord.tenants.index'],
        ['label' => 'Features', 'route' => 'landlord.features.index'],
        ['label' => 'Licenses', 'route' => 'landlord.licenses.index'],
        ['label' => 'Billing', 'route' => 'landlord.billing.index'],
        ['label' => 'Support', 'route' => 'landlord.support.index'],
    ];
@endphp
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Aegoryx Admin')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-neutral-950 text-neutral-100 antialiased">
    <div class="mx-auto flex min-h-screen w-full max-w-7xl">
        <aside class="hidden w-64 shrink-0 border-r border-neutral-800 px-5 py-6 md:block">
            <div>
                <p class="text-lg font-semibold">Aegoryx</p>
                <p class="mt-1 text-xs uppercase tracking-wide text-neutral-500">Landlord console</p>
            </div>

            <nav class="mt-8 space-y-1" aria-label="Admin navigation">
                @foreach ($navigation as $item)
                    <a
                        href="{{ route($item['route']) }}"
                        wire:navigate
                        class="block rounded px-3 py-2 text-sm {{ request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*') ? 'bg-sky-500 text-white' : 'text-neutral-300 hover:bg-neutral-900 hover:text-white' }}"
                    >
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="border-b border-neutral-800 px-5 py-4 md:px-8">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h1 class="text-xl font-semibold">@yield('heading', 'Admin Console')</h1>
                        <p class="mt-1 text-sm text-neutral-400">@yield('subheading', 'System-wide controls for Aegoryx.')</p>
                    </div>

                    <form method="POST" action="{{ route('landlord.logout') }}">
                        @csrf
                        <button type="submit" class="rounded border border-neutral-700 px-4 py-2 text-sm text-neutral-200 hover:border-neutral-500">
                            Sign out
                        </button>
                    </form>
                </div>

                <nav class="mt-5 flex gap-2 overflow-x-auto md:hidden" aria-label="Admin navigation">
                    @foreach ($navigation as $item)
                        <a
                            href="{{ route($item['route']) }}"
                            wire:navigate
                            class="shrink-0 rounded px-3 py-2 text-sm {{ request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*') ? 'bg-sky-500 text-white' : 'bg-neutral-900 text-neutral-300' }}"
                        >
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>
            </header>

            <main class="flex-1 px-5 py-6 md:px-8">
                @if (session('success'))
                    <div class="mb-5 rounded border border-emerald-800 bg-emerald-950 px-4 py-3 text-sm text-emerald-200">
                        {{ session('success') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
