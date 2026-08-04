@php
    $currentTheme = auth('admin')->user()?->theme?->value ?? 'light';

    $navigation = [
        ['label' => __('common.dashboard'), 'route' => 'admin.dashboard'],
        ['label' => __('common.tenants'), 'route' => 'admin.tenants.index'],
        ['label' => __('common.licenses'), 'route' => 'admin.licenses.index'],
        ['label' => __('common.billing'), 'route' => 'admin.billing.index'],
        ['label' => __('common.support'), 'route' => 'admin.support.index'],
        ['label' => __('audit_view.audit_log'), 'route' => 'admin.audit.index'],
        ['label' => __('common.security'), 'route' => 'admin.security.index'],
    ];

    $supportSession = session('admin_support_session_id')
        ? \App\Models\System\SupportSession::query()
            ->with('tenant')
            ->whereKey(session('admin_support_session_id'))
            ->where('actor_id', auth('admin')->id())
            ->where('status', \App\Modules\AdminConsole\Enums\SupportSessionStatus::Active->value)
            ->first()
        : null;
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ $currentTheme }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('app.admin_title'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="ds-app antialiased">
    <div class="ds-shell flex">
        <aside class="ui-sidebar ui-shell-border hidden w-64 shrink-0 border-r px-5 py-6 md:block">
            <div>
                <p class="text-lg font-semibold">Aegoryx</p>
                <p class="ui-caption mt-1 uppercase tracking-wide">{{ __('admin.console') }}</p>
            </div>

            <nav class="mt-8 space-y-1" aria-label="{{ __('admin.navigation_label') }}">
                @foreach ($navigation as $item)
                    <a
                        href="{{ route($item['route']) }}"
                        wire:navigate
                        class="ui-nav-link block rounded px-3 py-2 text-sm {{ request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*') ? 'ui-nav-link-active' : '' }}"
                    >
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="ui-header ui-shell-border border-b px-5 py-4 md:px-8">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h1 class="ui-heading-1">@yield('heading', __('admin.admin_console'))</h1>
                        <p class="ui-body mt-1">@yield('subheading', __('admin.system_controls'))</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <x-theme.switcher :theme="$currentTheme" :action="route('admin.theme.update')" />
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <x-ui.button type="submit" variant="secondary" size="sm">
                                {{ __('admin.sign_out') }}
                            </x-ui.button>
                        </form>
                    </div>
                </div>

                <nav class="mt-5 flex gap-2 overflow-x-auto md:hidden" aria-label="{{ __('admin.navigation_label') }}">
                    @foreach ($navigation as $item)
                        <a
                            href="{{ route($item['route']) }}"
                            wire:navigate
                            class="shrink-0 rounded px-3 py-2 text-sm {{ request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*') ? 'ui-nav-pill-active' : 'ui-nav-pill' }}"
                        >
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>
            </header>

            <main class="flex-1 px-5 py-6 md:px-8">
                @if ($supportSession && $supportSession->expires_at->isFuture())
                    <x-ui.alert variant="warning" class="mb-5">
                        {{ __('admin.support_mode_banner', ['tenant' => $supportSession->tenant?->name, 'expires' => $supportSession->expires_at->format('Y-m-d H:i')]) }}
                        <a href="{{ route('admin.support.index') }}" wire:navigate class="ui-link ml-2 underline">
                            {{ __('common.manage') }}
                        </a>
                    </x-ui.alert>
                @endif

                @if (session('success'))
                    <x-ui.alert variant="success" class="mb-5">
                        {{ session('success') }}
                    </x-ui.alert>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
