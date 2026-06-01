<div class="space-y-5">
    @if (session('success'))
        <div class="rounded border border-emerald-800 bg-emerald-950 px-4 py-3 text-sm text-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    @if ($currentSupportSession)
        <section class="rounded border border-amber-700 bg-amber-950 p-5 text-amber-100">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold">Support mode active</h2>
                    <p class="mt-2 text-sm">
                        Tenant: {{ $currentSupportSession->tenant?->name }} ({{ $currentSupportSession->tenant?->slug }})
                    </p>
                    <p class="mt-1 text-sm">Expires: {{ $currentSupportSession->expires_at->format('Y-m-d H:i') }}</p>
                    <p class="mt-1 text-sm">Reason: {{ $currentSupportSession->reason }}</p>
                </div>

                <form wire:submit="end">
                    <button type="submit" wire:loading.attr="disabled" class="rounded border border-amber-500 px-4 py-2 text-sm font-medium hover:bg-amber-900 disabled:cursor-not-allowed disabled:opacity-70">
                        End session
                    </button>
                </form>
            </div>
        </section>
    @endif

    <div class="grid gap-5 lg:grid-cols-[360px_1fr]">
        <section class="rounded border border-neutral-800 bg-neutral-900 p-5">
            <h2 class="text-lg font-semibold">Start support session</h2>

            <form wire:submit="start" class="mt-5 space-y-4">
                <div>
                    <label for="tenant_id" class="block text-sm font-medium text-neutral-300">Tenant</label>
                    <select id="tenant_id" wire:model="tenantId" class="mt-2 block w-full rounded border border-neutral-700 bg-neutral-950 px-3 py-2 text-neutral-100 outline-none focus:border-sky-400">
                        @foreach ($tenants as $tenant)
                            <option value="{{ $tenant->id }}">{{ $tenant->name }} ({{ $tenant->slug }})</option>
                        @endforeach
                    </select>
                    @error('tenantId')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="duration" class="block text-sm font-medium text-neutral-300">Duration</label>
                    <select id="duration" wire:model="durationMinutes" class="mt-2 block w-full rounded border border-neutral-700 bg-neutral-950 px-3 py-2 text-neutral-100 outline-none focus:border-sky-400">
                        <option value="15">15 minutes</option>
                        <option value="30">30 minutes</option>
                        <option value="60">60 minutes</option>
                        <option value="120">120 minutes</option>
                    </select>
                    @error('durationMinutes')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="reason" class="block text-sm font-medium text-neutral-300">Reason</label>
                    <textarea id="reason" wire:model="reason" rows="5" class="mt-2 block w-full rounded border border-neutral-700 bg-neutral-950 px-3 py-2 text-neutral-100 outline-none focus:border-sky-400"></textarea>
                    @error('reason')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" wire:loading.attr="disabled" class="w-full rounded bg-sky-500 px-4 py-2 font-medium text-white hover:bg-sky-400 disabled:cursor-not-allowed disabled:opacity-70">
                    Start support session
                </button>
            </form>
        </section>

        <section class="overflow-hidden rounded border border-neutral-800 bg-neutral-900">
            <div class="border-b border-neutral-800 px-5 py-4">
                <p class="text-sm text-neutral-400">Support sessions require a reason, expiration, actor, and audit trail.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-neutral-800 text-sm">
                    <thead class="bg-neutral-950 text-left text-neutral-400">
                        <tr>
                            <th class="px-5 py-3 font-medium">Tenant</th>
                            <th class="px-5 py-3 font-medium">Actor</th>
                            <th class="px-5 py-3 font-medium">Status</th>
                            <th class="px-5 py-3 font-medium">Reason</th>
                            <th class="px-5 py-3 font-medium">Expires</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-800">
                        @forelse ($supportSessions as $supportSession)
                            <tr wire:key="support-session-{{ $supportSession->id }}">
                                <td class="px-5 py-4">
                                    <div class="font-medium text-neutral-100">{{ $supportSession->tenant?->name ?? 'Deleted tenant' }}</div>
                                    <div class="mt-1 font-mono text-xs text-neutral-500">{{ $supportSession->tenant?->slug }}</div>
                                </td>
                                <td class="px-5 py-4 text-neutral-300">{{ $supportSession->actor?->email }}</td>
                                <td class="px-5 py-4 text-neutral-300">{{ $supportSession->status->value }}</td>
                                <td class="px-5 py-4 text-neutral-300">{{ $supportSession->reason }}</td>
                                <td class="px-5 py-4 text-neutral-400">{{ $supportSession->expires_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-neutral-400">No support sessions yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($supportSessions->hasPages())
                <div class="border-t border-neutral-800 px-5 py-4">
                    {{ $supportSessions->links() }}
                </div>
            @endif
        </section>
    </div>
</div>
