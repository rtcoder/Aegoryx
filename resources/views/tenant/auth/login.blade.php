<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('app.tenant_login_title') }}</title>
    <x-theme.bootstrap />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="ds-app antialiased">
    <livewire:tenant.auth.login-form />
    @livewireScripts
</body>
</html>
