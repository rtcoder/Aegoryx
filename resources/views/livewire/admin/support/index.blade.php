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
                    <h2 class="text-lg font-semibold">{{ __('support.mode_active') }}</h2>
                    <p class="mt-2 text-sm">
                        {{ __('support.tenant_line', ['tenant' => $currentSupportSession->tenant?->name, 'slug' => $currentSupportSession->tenant?->slug]) }}
                    </p>
                    <p class="mt-1 text-sm">{{ __('support.expires_line', ['expires' => $currentSupportSession->expires_at->format('Y-m-d H:i')]) }}</p>
                    <p class="mt-1 text-sm">{{ __('support.reason_line', ['reason' => $currentSupportSession->reason]) }}</p>
                </div>

                <form wire:submit="end">
                    <button type="submit" wire:loading.attr="disabled" class="rounded border border-amber-500 px-4 py-2 text-sm font-medium hover:bg-amber-900 disabled:cursor-not-allowed disabled:opacity-70">
                        {{ __('support.end_session') }}
                    </button>
                </form>
            </div>
        </section>
    @endif

    <div class="grid gap-5 lg:grid-cols-[360px_1fr]">
        <section class="rounded border border-neutral-800 bg-neutral-900 p-5">
            <h2 class="text-lg font-semibold">{{ __('support.start_title') }}</h2>

            <form wire:submit="start" class="mt-5 space-y-4">
                <div>
                    <label for="tenant_id" class="block text-sm font-medium text-neutral-300">{{ __('common.tenant') }}</label>
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
                    <label for="duration" class="block text-sm font-medium text-neutral-300">{{ __('support.duration') }}</label>
                    <select id="duration" wire:model="durationMinutes" class="mt-2 block w-full rounded border border-neutral-700 bg-neutral-950 px-3 py-2 text-neutral-100 outline-none focus:border-sky-400">
                        <option value="15">{{ __('support.duration_minutes', ['minutes' => 15]) }}</option>
                        <option value="30">{{ __('support.duration_minutes', ['minutes' => 30]) }}</option>
                        <option value="60">{{ __('support.duration_minutes', ['minutes' => 60]) }}</option>
                        <option value="120">{{ __('support.duration_minutes', ['minutes' => 120]) }}</option>
                    </select>
                    @error('durationMinutes')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="reason" class="block text-sm font-medium text-neutral-300">{{ __('common.reason') }}</label>
                    <textarea id="reason" wire:model="reason" rows="5" class="mt-2 block w-full rounded border border-neutral-700 bg-neutral-950 px-3 py-2 text-neutral-100 outline-none focus:border-sky-400"></textarea>
                    @error('reason')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" wire:loading.attr="disabled" class="w-full rounded bg-sky-500 px-4 py-2 font-medium text-white hover:bg-sky-400 disabled:cursor-not-allowed disabled:opacity-70">
                    {{ __('support.start_button') }}
                </button>
            </form>
        </section>

        <section class="overflow-hidden rounded border border-neutral-800 bg-neutral-900">
            <div class="border-b border-neutral-800 px-5 py-4">
                <p class="text-sm text-neutral-400">{{ __('support.index_note') }}</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-neutral-800 text-sm">
                    <thead class="bg-neutral-950 text-left text-neutral-400">
                        <tr>
                            <th class="px-5 py-3 font-medium">{{ __('common.tenant') }}</th>
                            <th class="px-5 py-3 font-medium">{{ __('common.actor') }}</th>
                            <th class="px-5 py-3 font-medium">{{ __('common.status') }}</th>
                            <th class="px-5 py-3 font-medium">{{ __('common.reason') }}</th>
                            <th class="px-5 py-3 font-medium">{{ __('common.expires') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-800">
                        @forelse ($supportSessions as $supportSession)
                            <tr wire:key="support-session-{{ $supportSession->id }}">
                                <td class="px-5 py-4">
                                    <div class="font-medium text-neutral-100">{{ $supportSession->tenant?->name ?? __('common.deleted_tenant') }}</div>
                                    <div class="mt-1 font-mono text-xs text-neutral-500">{{ $supportSession->tenant?->slug }}</div>
                                </td>
                                <td class="px-5 py-4 text-neutral-300">{{ $supportSession->actor?->email }}</td>
                                <td class="px-5 py-4 text-neutral-300">{{ $supportSession->status->value }}</td>
                                <td class="px-5 py-4 text-neutral-300">{{ $supportSession->reason }}</td>
                                <td class="px-5 py-4 text-neutral-400">{{ $supportSession->expires_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-neutral-400">{{ __('support.empty') }}</td>
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
