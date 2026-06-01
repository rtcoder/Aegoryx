<div>
    @if (session('success'))
        <div class="mb-5 rounded border border-emerald-800 bg-emerald-950 px-4 py-3 text-sm text-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-5 lg:grid-cols-[1fr_320px]">
        <section class="rounded border border-neutral-800 bg-neutral-900 p-5">
            <h2 class="text-lg font-semibold">License state</h2>

            <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-xs uppercase tracking-wide text-neutral-500">Tenant</dt>
                    <dd class="mt-1 text-neutral-100">{{ $license->tenant?->name ?? 'Unassigned' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-neutral-500">Status</dt>
                    <dd class="mt-1 text-neutral-100">{{ $license->status->value }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-neutral-500">Type</dt>
                    <dd class="mt-1 text-neutral-100">{{ $license->type }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-neutral-500">Expires</dt>
                    <dd class="mt-1 text-neutral-100">{{ $license->expires_at?->format('Y-m-d H:i') ?? 'perpetual' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-neutral-500">Issued</dt>
                    <dd class="mt-1 text-neutral-100">{{ $license->issued_at?->format('Y-m-d H:i') ?? 'not recorded' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-neutral-500">Last verified</dt>
                    <dd class="mt-1 text-neutral-100">{{ $license->last_verified_at?->format('Y-m-d H:i') ?? 'never' }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs uppercase tracking-wide text-neutral-500">License hash</dt>
                    <dd class="mt-1 break-all font-mono text-xs text-neutral-300">{{ $license->license_key_hash }}</dd>
                </div>
            </dl>
        </section>

        <aside class="space-y-5">
            <section class="rounded border border-neutral-800 bg-neutral-900 p-5">
                <h2 class="text-lg font-semibold">Verify</h2>
                <p class="mt-2 text-sm leading-6 text-neutral-400">Recheck local license state from expiry and grace metadata.</p>

                <form wire:submit="verify" class="mt-4">
                    <button type="submit" wire:loading.attr="disabled" class="w-full rounded bg-sky-500 px-4 py-2 font-medium text-white hover:bg-sky-400 disabled:cursor-not-allowed disabled:opacity-70">
                        Verify license
                    </button>
                </form>
            </section>

            <section class="rounded border border-neutral-800 bg-neutral-900 p-5">
                <h2 class="text-lg font-semibold">Verification metadata</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-neutral-500">Grace until</dt>
                        <dd class="mt-1 text-neutral-100">{{ $license->payload['grace_until'] ?? 'not set' }}</dd>
                    </div>
                </dl>
            </section>
        </aside>
    </div>
</div>
