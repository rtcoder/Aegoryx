<main class="flex min-h-screen items-center justify-center px-4 py-10">
    <section class="ui-card w-full max-w-md">
        <div class="ui-card-header">
            <h1 class="ui-heading-2">{{ __('two_factor.challenge_heading') }}</h1>
            <p class="ui-body mt-1">{{ __('two_factor.challenge_description', ['email' => $email]) }}</p>
        </div>

        <form wire:submit="verify" class="ui-card-body space-y-5">
            <div>
                <label for="code" class="ui-label">{{ __('two_factor.code') }}</label>
                <input id="code" wire:model="code" autocomplete="one-time-code" autofocus class="ui-input mt-2">
                @error('code')
                    <p class="ui-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3">
                <x-ui.button type="submit">
                    {{ __('two_factor.verify') }}
                </x-ui.button>
                <x-ui.button type="button" wire:click="cancel" variant="secondary">
                    {{ __('common.cancel') }}
                </x-ui.button>
            </div>
        </form>
    </section>
</main>
