<div class="space-y-5">
    <section class="ui-card">
        <div class="ui-card-header flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h2 class="ui-heading-2">{{ __('two_factor.landlord_2fa') }}</h2>
                <p class="ui-body mt-1">{{ __('two_factor.landlord_2fa_description') }}</p>
            </div>
            <x-ui.badge :variant="$identity->hasTwoFactorEnabled() ? 'success' : 'warning'">
                {{ $identity->hasTwoFactorEnabled() ? __('two_factor.enabled') : __('two_factor.disabled') }}
            </x-ui.badge>
        </div>

        <div class="ui-card-body space-y-5">
            @if ($identity->hasTwoFactorEnabled())
                <p class="ui-body">{{ __('two_factor.enabled_notice', ['date' => $identity->two_factor_confirmed_at?->format('Y-m-d H:i')]) }}</p>
                <x-ui.button type="button" wire:click="disable" variant="danger">
                    {{ __('two_factor.disable') }}
                </x-ui.button>
            @else
                <p class="ui-body">{{ __('two_factor.disabled_notice') }}</p>

                @if (! $pendingSecret)
                    <x-ui.button type="button" wire:click="generate">
                        {{ __('two_factor.start_setup') }}
                    </x-ui.button>
                @else
                    <div class="grid gap-5 lg:grid-cols-2">
                        <div class="ui-muted-panel space-y-3">
                            <h3 class="ui-heading-3">{{ __('two_factor.setup_secret') }}</h3>
                            <p class="ui-body">{{ __('two_factor.setup_secret_help') }}</p>
                            <div class="break-all rounded border border-[var(--ui-border)] bg-[var(--ui-surface)] p-3 font-mono text-sm text-[var(--ui-text)]">
                                {{ $pendingSecret }}
                            </div>
                            <p class="ui-help break-all">{{ $provisioningUri }}</p>
                        </div>

                        <div class="ui-muted-panel space-y-3">
                            <h3 class="ui-heading-3">{{ __('two_factor.recovery_codes') }}</h3>
                            <p class="ui-body">{{ __('two_factor.recovery_codes_help') }}</p>
                            <div class="grid gap-2 font-mono text-sm">
                                @foreach ($pendingRecoveryCodes as $recoveryCode)
                                    <div class="rounded border border-[var(--ui-border)] bg-[var(--ui-surface)] px-3 py-2">{{ $recoveryCode }}</div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <form wire:submit="enable" class="max-w-md space-y-4">
                        <div>
                            <label for="two_factor_code" class="ui-label">{{ __('two_factor.confirm_code') }}</label>
                            <input id="two_factor_code" wire:model="code" autocomplete="one-time-code" class="ui-input mt-2">
                            @error('code')
                                <p class="ui-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center gap-3">
                            <x-ui.button type="submit">
                                {{ __('two_factor.enable') }}
                            </x-ui.button>
                            <x-ui.button type="button" wire:click="generate" variant="secondary">
                                {{ __('two_factor.regenerate') }}
                            </x-ui.button>
                        </div>
                    </form>
                @endif
            @endif
        </div>
    </section>
</div>
