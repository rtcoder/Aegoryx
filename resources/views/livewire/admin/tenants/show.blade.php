<div>
    @if (session('success'))
        <x-ui.alert variant="success" class="mb-5">
            {{ session('success') }}
        </x-ui.alert>
    @endif

    <div class="grid gap-5 lg:grid-cols-[1fr_320px]">
        <section class="ui-card p-5">
            <h2 class="ui-heading-2">{{ __('tenants.information') }}</h2>

            <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                <div>
                    <dt class="ui-caption uppercase tracking-wide">{{ __('common.slug') }}</dt>
                    <dd class="mt-1 text-[var(--ui-text)]">{{ $tenant->slug }}</dd>
                </div>
                <div>
                    <dt class="ui-caption uppercase tracking-wide">{{ __('common.schema') }}</dt>
                    <dd class="mt-1 font-mono text-sm text-[var(--ui-text)]">{{ $tenant->schema_name }}</dd>
                </div>
                <div>
                    <dt class="ui-caption uppercase tracking-wide">{{ __('common.status') }}</dt>
                    <dd class="mt-1 text-[var(--ui-text)]">{{ $tenant->status->value }}</dd>
                </div>
                <div>
                    <dt class="ui-caption uppercase tracking-wide">{{ __('common.deployment') }}</dt>
                    <dd class="mt-1 text-[var(--ui-text)]">{{ $tenant->deployment_type->value }}</dd>
                </div>
                <div>
                    <dt class="ui-caption uppercase tracking-wide">{{ __('common.billing_model') }}</dt>
                    <dd class="mt-1 text-[var(--ui-text)]">{{ $tenant->billing_model->value }}</dd>
                </div>
                <div>
                    <dt class="ui-caption uppercase tracking-wide">{{ __('common.license_type') }}</dt>
                    <dd class="mt-1 text-[var(--ui-text)]">{{ $tenant->license_type->value }}</dd>
                </div>
            </dl>
        </section>

        <section class="ui-card p-5">
            <h2 class="ui-heading-2">{{ __('features.tenant_access') }}</h2>
            <p class="ui-body mt-2">{{ __('features.tenant_access_description') }}</p>

            <form wire:submit="saveFeatures" class="mt-5 space-y-4">
                <div class="grid gap-3 md:grid-cols-3">
                    @foreach ($systemFeatures as $feature)
                        @php($entitlement = $effectiveEntitlements[$feature->value] ?? null)
                        <div class="ui-muted-panel p-4">
                            <span class="flex items-start gap-3">
                                <input
                                    id="feature-{{ $feature->value }}"
                                    type="checkbox"
                                    wire:model="features.{{ $feature->value }}"
                                    class="mt-1 rounded border-[var(--ui-border)] bg-[var(--ui-surface)] text-[var(--ui-accent)] focus:ring-[var(--ui-focus)]"
                                >
                                <span>
                                    <label for="feature-{{ $feature->value }}" class="block text-sm font-medium text-[var(--ui-text)]">{{ $feature->label() }}</label>
                                    <span class="ui-caption mt-1 block leading-5">{{ $feature->description() }}</span>
                                    <span class="ui-caption mt-2 block font-mono">{{ $feature->value }}</span>
                                    <span class="mt-3 grid gap-1 text-xs text-[var(--ui-text-muted)]">
                                        <span>
                                            {{ __('features.effective_source') }}:
                                            <span class="text-[var(--ui-text)]">{{ __('features.source_labels.'.($entitlement['source'] ?? 'none')) }}</span>
                                        </span>
                                        @if ($entitlement['reason'] ?? null)
                                            <span>
                                                {{ __('features.effective_reason') }}:
                                                <span class="font-mono text-[var(--ui-text-muted)]">{{ $entitlement['reason'] }}</span>
                                            </span>
                                        @endif
                                    </span>
                                    @if (($entitlement['source'] ?? null) === \App\Modules\Entitlements\Enums\TenantFeatureSource::Manual->value)
                                        <button
                                            type="button"
                                            wire:click="clearFeatureOverride('{{ $feature->value }}')"
                                            wire:loading.attr="disabled"
                                            class="ui-link mt-3 text-xs disabled:cursor-not-allowed disabled:opacity-70"
                                        >
                                            {{ __('features.clear_override') }}
                                        </button>
                                    @endif
                                </span>
                            </span>
                        </div>
                    @endforeach
                </div>

                <div>
                    <label for="featureReason" class="ui-label">{{ __('common.reason') }}</label>
                    <textarea
                        id="featureReason"
                        wire:model="featureReason"
                        rows="3"
                        class="ui-textarea"
                    ></textarea>
                    @error('featureReason')
                        <p class="ui-error">{{ $message }}</p>
                    @enderror
                </div>

                <x-ui.button type="submit" wire:loading.attr="disabled" class="disabled:cursor-not-allowed disabled:opacity-70">
                    {{ __('features.save_access') }}
                </x-ui.button>
            </form>
        </section>

        <aside class="space-y-5">
            <section class="ui-card p-5">
                <h2 class="ui-heading-2">{{ __('common.status') }}</h2>

                <form wire:submit="updateStatus" class="mt-4 space-y-4">
                    <div>
                        <label for="status" class="ui-label">{{ __('tenants.tenant_status') }}</label>
                        <select
                            id="status"
                            wire:model="status"
                            class="ui-select"
                        >
                            <option value="active">{{ __('common.active') }}</option>
                            <option value="suspended">{{ __('common.suspended') }}</option>
                        </select>
                        @error('status')
                            <p class="ui-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <x-ui.button type="submit" wire:loading.attr="disabled" class="w-full disabled:cursor-not-allowed disabled:opacity-70">
                        {{ __('tenants.update_status') }}
                    </x-ui.button>
                </form>
            </section>

            <section class="ui-card p-5">
                <h2 class="ui-heading-2">{{ __('common.support') }}</h2>
                <p class="ui-body mt-2">{{ __('tenants.support_description') }}</p>
                <a href="{{ route('admin.support.index') }}" wire:navigate class="ui-link mt-4 inline-flex text-sm">
                    {{ __('tenants.open_support') }}
                </a>
            </section>
        </aside>
    </div>
</div>
