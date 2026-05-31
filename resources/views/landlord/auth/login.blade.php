<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aegoryx Landlord Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-neutral-950 text-neutral-100 antialiased">
    <main class="flex min-h-screen items-center justify-center px-6 py-12">
        <section class="w-full max-w-sm">
            <h1 class="text-2xl font-semibold">Aegoryx Admin</h1>
            <p class="mt-2 text-sm text-neutral-400">Landlord console</p>

            <form method="POST" action="{{ route('landlord.login.store') }}" class="mt-8 space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-neutral-200">Email</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        autocomplete="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        class="mt-2 block w-full rounded border border-neutral-700 bg-neutral-900 px-3 py-2 text-neutral-100 outline-none focus:border-sky-400"
                    >
                    @error('email')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-neutral-200">Password</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="current-password"
                        required
                        class="mt-2 block w-full rounded border border-neutral-700 bg-neutral-900 px-3 py-2 text-neutral-100 outline-none focus:border-sky-400"
                    >
                    @error('password')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full rounded bg-sky-500 px-4 py-2 font-medium text-white hover:bg-sky-400">
                    Sign in
                </button>
            </form>
        </section>
    </main>
</body>
</html>
