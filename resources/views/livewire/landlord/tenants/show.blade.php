<div>
    @if (session('success'))
        <div class="mb-5 rounded border border-emerald-800 bg-emerald-950 px-4 py-3 text-sm text-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-5 lg:grid-cols-[1fr_320px]">
        <section class="rounded border border-neutral-800 bg-neutral-900 p-5">
            <h2 class="text-lg font-semibold">{{ __('tenants.information') }}</h2>

            <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-xs uppercase tracking-wide text-neutral-500">{{ __('common.slug') }}</dt>
                    <dd class="mt-1 text-neutral-100">{{ $tenant->slug }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-neutral-500">{{ __('common.schema') }}</dt>
                    <dd class="mt-1 font-mono text-sm text-neutral-100">{{ $tenant->schema_name }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-neutral-500">{{ __('common.status') }}</dt>
                    <dd class="mt-1 text-neutral-100">{{ $tenant->status->value }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-neutral-500">{{ __('common.deployment') }}</dt>
                    <dd class="mt-1 text-neutral-100">{{ $tenant->deployment_type->value }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-neutral-500">{{ __('common.billing_model') }}</dt>
                    <dd class="mt-1 text-neutral-100">{{ $tenant->billing_model->value }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-neutral-500">{{ __('common.license_type') }}</dt>
                    <dd class="mt-1 text-neutral-100">{{ $tenant->license_type->value }}</dd>
                </div>
            </dl>
        </section>

        <section class="rounded border border-neutral-800 bg-neutral-900 p-5">
            <h2 class="text-lg font-semibold">{{ __('features.tenant_access') }}</h2>
            <p class="mt-2 text-sm leading-6 text-neutral-400">{{ __('features.tenant_access_description') }}</p>

            <form wire:submit="saveFeatures" class="mt-5 space-y-4">
                <div class="grid gap-3 md:grid-cols-3">
                    @foreach ($systemFeatures as $feature)
                        <label class="rounded border border-neutral-800 bg-neutral-950 p-4">
                            <span class="flex items-start gap-3">
                                <input
                                    type="checkbox"
                                    wire:model="features.{{ $feature->value }}"
                                    class="mt-1 rounded border-neutral-700 bg-neutral-950 text-sky-500 focus:ring-sky-400"
                                >
                                <span>
                                    <span class="block text-sm font-medium text-neutral-100">{{ $feature->label() }}</span>
                                    <span class="mt-1 block text-xs leading-5 text-neutral-500">{{ $feature->description() }}</span>
                                    <span class="mt-2 block font-mono text-xs text-neutral-500">{{ $feature->value }}</span>
                                </span>
                            </span>
                        </label>
                    @endforeach
                </div>

                <div>
                    <label for="featureReason" class="block text-sm font-medium text-neutral-300">{{ __('common.reason') }}</label>
                    <textarea
                        id="featureReason"
                        wire:model="featureReason"
                        rows="3"
                        class="mt-2 block w-full rounded border border-neutral-700 bg-neutral-950 px-3 py-2 text-neutral-100 outline-none focus:border-sky-400"
                    ></textarea>
                    @error('featureReason')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" wire:loading.attr="disabled" class="rounded bg-sky-500 px-4 py-2 font-medium text-white hover:bg-sky-400 disabled:cursor-not-allowed disabled:opacity-70">
                    {{ __('features.save_access') }}
                </button>
            </form>
        </section>

        <aside class="space-y-5">
            <section class="rounded border border-neutral-800 bg-neutral-900 p-5">
                <h2 class="text-lg font-semibold">{{ __('common.status') }}</h2>

                <form wire:submit="updateStatus" class="mt-4 space-y-4">
                    <div>
                        <label for="status" class="block text-sm font-medium text-neutral-300">{{ __('tenants.tenant_status') }}</label>
                        <select
                            id="status"
                            wire:model="status"
                            class="mt-2 block w-full rounded border border-neutral-700 bg-neutral-950 px-3 py-2 text-neutral-100 outline-none focus:border-sky-400"
                        >
                            <option value="active">{{ __('common.active') }}</option>
                            <option value="suspended">{{ __('common.suspended') }}</option>
                        </select>
                        @error('status')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" wire:loading.attr="disabled" class="w-full rounded bg-sky-500 px-4 py-2 font-medium text-white hover:bg-sky-400 disabled:cursor-not-allowed disabled:opacity-70">
                        {{ __('tenants.update_status') }}
                    </button>
                </form>
            </section>

            <section class="rounded border border-neutral-800 bg-neutral-900 p-5">
                <h2 class="text-lg font-semibold">{{ __('common.support') }}</h2>
                <p class="mt-2 text-sm leading-6 text-neutral-400">{{ __('tenants.support_description') }}</p>
                <a href="{{ route('landlord.support.index') }}" wire:navigate class="mt-4 inline-flex text-sm text-sky-300 hover:text-sky-200">
                    {{ __('tenants.open_support') }}
                </a>
            </section>
        </aside>
    </div>
</div>
