<main class="flex min-h-screen items-center justify-center px-6 py-12">
    <x-ui.card class="w-full max-w-sm">
        <div>
            <x-logo/>
            <h1 class="ui-heading-1 mt-6">{{ __('common.password_reset_heading') }}</h1>
            <p class="ui-body mt-2">{{ __('common.password_reset_description') }}</p>
        </div>

        <form wire:submit="resetPassword" class="mt-6 space-y-4">
            <x-form.input name="email" type="email" :label="__('common.email')" wire:model="email" autocomplete="email" autofocus />
            <x-form.input name="password" type="password" :label="__('common.new_password')" wire:model="password" autocomplete="new-password" />
            <x-form.input name="password_confirmation" type="password" :label="__('common.confirm_password')" wire:model="password_confirmation" autocomplete="new-password" />

            <x-ui.button type="submit" class="w-full disabled:cursor-not-allowed disabled:opacity-70" wire:loading.attr="disabled">
                {{ __('common.password_reset_save') }}
            </x-ui.button>
        </form>

        <p class="mt-5 text-center text-sm">
            <a href="{{ route('admin.login') }}" class="ui-link">{{ __('common.back_to_login') }}</a>
        </p>
    </x-ui.card>
</main>
