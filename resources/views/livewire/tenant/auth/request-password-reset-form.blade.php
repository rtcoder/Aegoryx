<main class="flex min-h-screen items-center justify-center px-6 py-12">
    <x-ui.card class="w-full max-w-sm">
        <div>
            <p class="ui-heading-2">Aegoryx</p>
            <h1 class="ui-heading-1 mt-6">{{ __('common.password_reset_request_heading') }}</h1>
            <p class="ui-body mt-2">{{ __('common.password_reset_request_description') }}</p>
        </div>

        @if (session('success'))
            <div class="mt-6 rounded border border-emerald-700 bg-emerald-950 px-4 py-3 text-sm text-emerald-100">
                {{ session('success') }}
            </div>
        @endif

        @if ($showDevToken)
            <div class="mt-4 rounded border border-sky-700 bg-sky-950 px-4 py-3 text-sm text-sky-100">
                <p class="font-medium">{{ __('common.password_reset_dev_token') }}</p>
                <p class="mt-2 break-all font-mono">{{ route('tenant.password.reset', ['token' => $token]) }}</p>
            </div>
        @endif

        <form wire:submit="requestReset" class="mt-6 space-y-4">
            <x-form.input name="email" type="email" :label="__('common.email')" wire:model="email" autocomplete="email" autofocus />

            <x-ui.button type="submit" class="w-full disabled:cursor-not-allowed disabled:opacity-70" wire:loading.attr="disabled">
                {{ __('common.password_reset_send') }}
            </x-ui.button>
        </form>

        <p class="mt-5 text-center text-sm">
            <a href="{{ route('tenant.login') }}" class="text-sky-300 hover:text-sky-200">{{ __('common.back_to_login') }}</a>
        </p>
    </x-ui.card>
</main>
