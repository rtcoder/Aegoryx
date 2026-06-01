<main class="flex min-h-screen items-center justify-center bg-neutral-950 px-6 py-12 text-neutral-100">
    <section class="w-full max-w-sm rounded border border-neutral-800 bg-neutral-900 p-6">
        <div>
            <p class="text-lg font-semibold">Aegoryx</p>
            <h1 class="mt-6 text-2xl font-semibold">{{ __('tenant_panel.login_heading') }}</h1>
            <p class="mt-2 text-sm text-neutral-400">{{ __('tenant_panel.login_description', ['tenant' => $tenant->name]) }}</p>
        </div>

        <form wire:submit="login" class="mt-6 space-y-4">
            <div>
                <label for="email" class="block text-sm font-medium text-neutral-300">{{ __('common.email') }}</label>
                <input
                    id="email"
                    type="email"
                    wire:model="email"
                    autocomplete="email"
                    class="mt-2 block w-full rounded border border-neutral-700 bg-neutral-950 px-3 py-2 text-neutral-100 outline-none focus:border-sky-400"
                    autofocus
                >
                @error('email')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-neutral-300">{{ __('common.password') }}</label>
                <input
                    id="password"
                    type="password"
                    wire:model="password"
                    autocomplete="current-password"
                    class="mt-2 block w-full rounded border border-neutral-700 bg-neutral-950 px-3 py-2 text-neutral-100 outline-none focus:border-sky-400"
                >
                @error('password')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <button
                type="submit"
                class="w-full rounded bg-sky-500 px-4 py-2 font-medium text-white hover:bg-sky-400 disabled:cursor-not-allowed disabled:opacity-70"
                wire:loading.attr="disabled"
            >
                {{ __('tenant_panel.sign_in') }}
            </button>
        </form>
    </section>
</main>
