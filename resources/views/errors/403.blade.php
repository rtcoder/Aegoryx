<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('errors.403_title') }} | Aegoryx</title>
    <x-theme.bootstrap />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="ds-app antialiased">
    <main class="mx-auto flex min-h-screen w-full max-w-2xl items-center px-6 py-12">
        <section class="ui-card w-full p-6">
            <p class="ui-caption uppercase tracking-wide">403</p>
            <h1 class="ui-heading-1 mt-3">{{ __('errors.403_title') }}</h1>
            <p class="ui-body mt-3">
                {{ $exception->getMessage() ?: __('errors.403_default') }}
            </p>
        </section>
    </main>
</body>
</html>
