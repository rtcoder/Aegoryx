<main class="flex min-h-screen items-center justify-center px-6 py-12">
    <x-ui.card class="w-full max-w-sm">
        <div>
            <x-logo/>
            <h1 class="ui-heading-1 mt-6">{{ __('admin.login_heading') }}</h1>
            <p class="ui-body mt-2">{{ __('admin.login_description') }}</p>
        </div>

        @if (session('success'))
            <x-ui.alert variant="success" class="mt-6">
                {{ session('success') }}
            </x-ui.alert>
        @endif

        <form wire:submit="login" class="mt-6 space-y-4">
            <x-form.input name="email" type="email" :label="__('common.email')" wire:model="email" autocomplete="email" autofocus />
            <x-form.input name="password" type="password" :label="__('common.password')" wire:model="password" autocomplete="current-password" />

            <x-ui.button
                type="submit"
                class="w-full disabled:cursor-not-allowed disabled:opacity-70"
                wire:loading.attr="disabled"
            >
                {{ __('admin.sign_in') }}
            </x-ui.button>
        </form>

        <p class="mt-5 text-center text-sm">
            <a href="{{ route('admin.password.request') }}" class="ui-link">{{ __('common.forgot_password') }}</a>
        </p>
    </x-ui.card>
</main>
