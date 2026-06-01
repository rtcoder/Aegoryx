<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Aegoryx Tenant Panel')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-neutral-950 text-neutral-100 antialiased">
    <div class="mx-auto min-h-screen w-full max-w-7xl px-5 py-6 md:px-8">
        <header class="border-b border-neutral-800 pb-5">
            <p class="text-lg font-semibold">Aegoryx</p>
            <p class="mt-1 text-sm text-neutral-400">{{ $tenant->name }}</p>
        </header>

        <main class="py-6">
            @yield('content')
        </main>
    </div>

    @livewireScripts
</body>
</html>
