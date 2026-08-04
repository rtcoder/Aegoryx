<div>
    @if (session('success'))
        <x-ui.alert variant="success" class="mb-5">
            {{ session('success') }}
        </x-ui.alert>
    @endif

    <div class="grid gap-5 lg:grid-cols-[1fr_320px]">
        <section class="ui-card p-5">
            <h2 class="ui-heading-2">{{ __('licenses.state') }}</h2>

            <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                <div>
                    <dt class="ui-caption uppercase tracking-wide">{{ __('common.tenant') }}</dt>
                    <dd class="mt-1 text-[var(--ui-text)]">{{ $license->tenant?->name ?? __('common.unassigned') }}</dd>
                </div>
                <div>
                    <dt class="ui-caption uppercase tracking-wide">{{ __('common.status') }}</dt>
                    <dd class="mt-1 text-[var(--ui-text)]">{{ $license->status->value }}</dd>
                </div>
                <div>
                    <dt class="ui-caption uppercase tracking-wide">{{ __('common.type') }}</dt>
                    <dd class="mt-1 text-[var(--ui-text)]">{{ $license->type }}</dd>
                </div>
                <div>
                    <dt class="ui-caption uppercase tracking-wide">{{ __('common.expires') }}</dt>
                    <dd class="mt-1 text-[var(--ui-text)]">{{ $license->expires_at?->format('Y-m-d H:i') ?? __('common.perpetual') }}</dd>
                </div>
                <div>
                    <dt class="ui-caption uppercase tracking-wide">{{ __('licenses.issued') }}</dt>
                    <dd class="mt-1 text-[var(--ui-text)]">{{ $license->issued_at?->format('Y-m-d H:i') ?? __('common.not_recorded') }}</dd>
                </div>
                <div>
                    <dt class="ui-caption uppercase tracking-wide">{{ __('common.last_verified') }}</dt>
                    <dd class="mt-1 text-[var(--ui-text)]">{{ $license->last_verified_at?->format('Y-m-d H:i') ?? __('common.never') }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="ui-caption uppercase tracking-wide">{{ __('common.license_hash') }}</dt>
                    <dd class="mt-1 break-all font-mono text-xs text-[var(--ui-text-muted)]">{{ $license->license_key_hash }}</dd>
                </div>
            </dl>
        </section>

        <aside class="space-y-5">
            <section class="ui-card p-5">
                <h2 class="ui-heading-2">{{ __('licenses.verify') }}</h2>
                <p class="ui-body mt-2">{{ __('licenses.verify_description') }}</p>

                <form wire:submit="verify" class="mt-4">
                    <x-ui.button type="submit" class="w-full disabled:cursor-not-allowed disabled:opacity-70" wire:loading.attr="disabled">
                        {{ __('licenses.verify_button') }}
                    </x-ui.button>
                </form>
            </section>

            <section class="ui-card p-5">
                <h2 class="ui-heading-2">{{ __('licenses.verification_metadata') }}</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div>
                        <dt class="ui-caption uppercase tracking-wide">{{ __('licenses.grace_until') }}</dt>
                        <dd class="mt-1 text-[var(--ui-text)]">{{ $license->payload['grace_until'] ?? __('common.not_set') }}</dd>
                    </div>
                </dl>
            </section>
        </aside>
    </div>
</div>
