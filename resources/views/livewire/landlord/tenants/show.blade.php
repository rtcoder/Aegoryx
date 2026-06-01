<div>
    @if (session('success'))
        <div class="mb-5 rounded border border-emerald-800 bg-emerald-950 px-4 py-3 text-sm text-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-5 lg:grid-cols-[1fr_320px]">
        <section class="rounded border border-neutral-800 bg-neutral-900 p-5">
            <h2 class="text-lg font-semibold">Tenant information</h2>

            <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-xs uppercase tracking-wide text-neutral-500">Slug</dt>
                    <dd class="mt-1 text-neutral-100">{{ $tenant->slug }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-neutral-500">Schema</dt>
                    <dd class="mt-1 font-mono text-sm text-neutral-100">{{ $tenant->schema_name }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-neutral-500">Status</dt>
                    <dd class="mt-1 text-neutral-100">{{ $tenant->status->value }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-neutral-500">Deployment</dt>
                    <dd class="mt-1 text-neutral-100">{{ $tenant->deployment_type->value }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-neutral-500">Billing model</dt>
                    <dd class="mt-1 text-neutral-100">{{ $tenant->billing_model->value }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-neutral-500">License type</dt>
                    <dd class="mt-1 text-neutral-100">{{ $tenant->license_type->value }}</dd>
                </div>
            </dl>
        </section>

        <aside class="space-y-5">
            <section class="rounded border border-neutral-800 bg-neutral-900 p-5">
                <h2 class="text-lg font-semibold">Status</h2>

                <form wire:submit="updateStatus" class="mt-4 space-y-4">
                    <div>
                        <label for="status" class="block text-sm font-medium text-neutral-300">Tenant status</label>
                        <select
                            id="status"
                            wire:model="status"
                            class="mt-2 block w-full rounded border border-neutral-700 bg-neutral-950 px-3 py-2 text-neutral-100 outline-none focus:border-sky-400"
                        >
                            <option value="active">active</option>
                            <option value="suspended">suspended</option>
                        </select>
                        @error('status')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" wire:loading.attr="disabled" class="w-full rounded bg-sky-500 px-4 py-2 font-medium text-white hover:bg-sky-400 disabled:cursor-not-allowed disabled:opacity-70">
                        Update status
                    </button>
                </form>
            </section>

            <section class="rounded border border-neutral-800 bg-neutral-900 p-5">
                <h2 class="text-lg font-semibold">Support</h2>
                <p class="mt-2 text-sm leading-6 text-neutral-400">Support sessions will be handled by the audited support access flow.</p>
                <a href="{{ route('landlord.support.index') }}" wire:navigate class="mt-4 inline-flex text-sm text-sky-300 hover:text-sky-200">
                    Open support
                </a>
            </section>
        </aside>
    </div>
</div>
