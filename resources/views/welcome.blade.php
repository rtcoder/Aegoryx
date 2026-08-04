<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('welcome.title') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="ds-app antialiased">
    <main class="mx-auto flex min-h-screen w-full max-w-3xl items-center px-6 py-12">
        <section>
            <p class="ui-caption uppercase tracking-wide">Aegoryx</p>
            <h1 class="ui-heading-1 mt-3">{{ __('welcome.heading') }}</h1>
            <p class="ui-body mt-4 max-w-xl">
                {{ __('welcome.description') }}
            </p>
            <x-ui.button :href="'http://'.config('aegoryx.admin.domain')" class="mt-6">
                {{ __('welcome.admin_link') }}
            </x-ui.button>
        </section>
    </main>
</body>
</html>
