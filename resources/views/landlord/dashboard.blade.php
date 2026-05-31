<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aegoryx Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-neutral-950 text-neutral-100 antialiased">
    <main class="mx-auto flex min-h-screen w-full max-w-5xl flex-col px-6 py-8">
        <header class="flex items-center justify-between border-b border-neutral-800 pb-6">
            <div>
                <h1 class="text-2xl font-semibold">Aegoryx Admin</h1>
                <p class="mt-1 text-sm text-neutral-400">Landlord console</p>
            </div>

            <form method="POST" action="{{ route('landlord.logout') }}">
                @csrf
                <button type="submit" class="rounded border border-neutral-700 px-4 py-2 text-sm text-neutral-200 hover:border-neutral-500">
                    Sign out
                </button>
            </form>
        </header>

        <section class="grid flex-1 place-items-center">
            <div class="text-center">
                <p class="text-sm uppercase tracking-wide text-sky-300">Logged in</p>
                <h2 class="mt-3 text-3xl font-semibold">Superadmin panel is ready.</h2>
            </div>
        </section>
    </main>
</body>
</html>
