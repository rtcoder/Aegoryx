<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('welcome.title') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-neutral-950 text-neutral-100 antialiased">
    <main class="mx-auto flex min-h-screen w-full max-w-3xl items-center px-6 py-12">
        <section>
            <p class="text-sm uppercase tracking-wide text-neutral-500">Aegoryx</p>
            <h1 class="mt-3 text-3xl font-semibold">{{ __('welcome.heading') }}</h1>
            <p class="mt-4 max-w-xl text-sm leading-6 text-neutral-400">
                {{ __('welcome.description') }}
            </p>
            <a
                href="http://{{ config('aegoryx.landlord.domain') }}"
                class="mt-6 inline-flex rounded bg-sky-500 px-4 py-2 text-sm font-medium text-white hover:bg-sky-400"
            >
                {{ __('welcome.admin_link') }}
            </a>
        </section>
    </main>
</body>
</html>
