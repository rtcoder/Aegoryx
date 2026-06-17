<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@php
    $navigation = [
        ['label' => __('common.dashboard'), 'route' => 'landlord.dashboard'],
        ['label' => __('common.tenants'), 'route' => 'landlord.tenants.index'],
        ['label' => __('common.licenses'), 'route' => 'landlord.licenses.index'],
        ['label' => __('common.billing'), 'route' => 'landlord.billing.index'],
        ['label' => __('common.support'), 'route' => 'landlord.support.index'],
        ['label' => __('common.security'), 'route' => 'landlord.security.index'],
    ];

    $supportSession = session('landlord_support_session_id')
        ? \App\Models\Landlord\SupportSession::query()
            ->with('tenant')
            ->whereKey(session('landlord_support_session_id'))
            ->where('actor_id', auth('landlord')->id())
            ->where('status', \App\Modules\AdminConsole\Enums\SupportSessionStatus::Active->value)
            ->first()
        : null;
@endphp
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', __('app.admin_title'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="ds-app antialiased">
    <div class="ds-shell flex">
        <aside class="hidden w-64 shrink-0 border-r border-neutral-800 px-5 py-6 md:block">
            <div>
                <p class="text-lg font-semibold">Aegoryx</p>
                <p class="mt-1 text-xs uppercase tracking-wide text-neutral-500">{{ __('landlord.console') }}</p>
            </div>

            <nav class="mt-8 space-y-1" aria-label="{{ __('landlord.navigation_label') }}">
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
                        <h1 class="text-xl font-semibold">@yield('heading', __('landlord.admin_console'))</h1>
                        <p class="mt-1 text-sm text-neutral-400">@yield('subheading', __('landlord.system_controls'))</p>
                    </div>

                    <form method="POST" action="{{ route('landlord.logout') }}">
                        @csrf
                        <x-ui.button type="submit" variant="secondary" size="sm">
                            {{ __('landlord.sign_out') }}
                        </x-ui.button>
                    </form>
                </div>

                <nav class="mt-5 flex gap-2 overflow-x-auto md:hidden" aria-label="{{ __('landlord.navigation_label') }}">
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
                @if ($supportSession && $supportSession->expires_at->isFuture())
                    <div class="mb-5 rounded border border-[var(--ui-warning)] bg-[var(--ui-warning-soft)] px-4 py-3 text-sm text-[var(--ui-warning)]">
                        {{ __('landlord.support_mode_banner', ['tenant' => $supportSession->tenant?->name, 'expires' => $supportSession->expires_at->format('Y-m-d H:i')]) }}
                        <a href="{{ route('landlord.support.index') }}" wire:navigate class="ui-link ml-2 underline">
                            {{ __('common.manage') }}
                        </a>
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-5 rounded border border-[var(--ui-success)] bg-[var(--ui-success-soft)] px-4 py-3 text-sm text-[var(--ui-success)]">
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
