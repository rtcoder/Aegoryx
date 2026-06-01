<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('errors.403_title') }} | Aegoryx</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-neutral-950 text-neutral-100 antialiased">
    <main class="mx-auto flex min-h-screen w-full max-w-2xl items-center px-6 py-12">
        <section class="w-full rounded border border-neutral-800 bg-neutral-900 p-6">
            <p class="text-xs uppercase tracking-wide text-neutral-500">403</p>
            <h1 class="mt-3 text-2xl font-semibold">{{ __('errors.403_title') }}</h1>
            <p class="mt-3 text-sm leading-6 text-neutral-400">
                {{ $exception->getMessage() ?: __('errors.403_default') }}
            </p>
        </section>
    </main>
</body>
</html>
