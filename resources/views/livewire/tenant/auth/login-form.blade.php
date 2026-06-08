<main class="flex min-h-screen items-center justify-center px-6 py-12">
    <x-ui.card class="w-full max-w-sm">
        <div>
            <p class="ui-heading-2">Aegoryx</p>
            <h1 class="ui-heading-1 mt-6">{{ __('tenant_panel.login_heading') }}</h1>
            <p class="ui-body mt-2">{{ __('tenant_panel.login_description', ['tenant' => $tenant->name]) }}</p>
        </div>

        <form wire:submit="login" class="mt-6 space-y-4">
            <x-form.input name="email" type="email" :label="__('common.email')" wire:model="email" autocomplete="email" autofocus />
            <x-form.input name="password" type="password" :label="__('common.password')" wire:model="password" autocomplete="current-password" />

            <x-ui.button
                type="submit"
                class="w-full disabled:cursor-not-allowed disabled:opacity-70"
                wire:loading.attr="disabled"
            >
                {{ __('tenant_panel.sign_in') }}
            </x-ui.button>
        </form>
    </x-ui.card>
</main>
