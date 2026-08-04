<div class="space-y-5">
    @if (session('success'))
        <x-ui.alert variant="success">
            {{ session('success') }}
        </x-ui.alert>
    @endif

    @if ($currentSupportSession)
        <x-ui.alert variant="warning" class="p-5">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h2 class="ui-heading-2">{{ __('support.mode_active') }}</h2>
                    <p class="mt-2 text-sm text-[var(--ui-warning)]">
                        {{ __('support.tenant_line', ['tenant' => $currentSupportSession->tenant?->name, 'slug' => $currentSupportSession->tenant?->slug]) }}
                    </p>
                    <p class="mt-1 text-sm text-[var(--ui-warning)]">{{ __('support.expires_line', ['expires' => $currentSupportSession->expires_at->format('Y-m-d H:i')]) }}</p>
                    <p class="mt-1 text-sm text-[var(--ui-warning)]">{{ __('support.reason_line', ['reason' => $currentSupportSession->reason]) }}</p>
                </div>

                <form wire:submit="end">
                    <x-ui.button type="submit" wire:loading.attr="disabled" variant="secondary" class="disabled:cursor-not-allowed disabled:opacity-70">
                        {{ __('support.end_session') }}
                    </x-ui.button>
                </form>
            </div>
        </x-ui.alert>
    @endif

    <div class="grid gap-5 lg:grid-cols-[360px_1fr]">
        <section class="ui-card p-5">
            <h2 class="ui-heading-2">{{ __('support.start_title') }}</h2>

            <form wire:submit="start" class="mt-5 space-y-4">
                <div>
                    <label for="tenant_id" class="ui-label">{{ __('common.tenant') }}</label>
                    <select id="tenant_id" wire:model="tenantId" class="ui-select">
                        @foreach ($tenants as $tenant)
                            <option value="{{ $tenant->id }}">{{ $tenant->name }} ({{ $tenant->slug }})</option>
                        @endforeach
                    </select>
                    @error('tenantId')
                        <p class="ui-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="duration" class="ui-label">{{ __('support.duration') }}</label>
                    <select id="duration" wire:model="durationMinutes" class="ui-select">
                        <option value="15">{{ __('support.duration_minutes', ['minutes' => 15]) }}</option>
                        <option value="30">{{ __('support.duration_minutes', ['minutes' => 30]) }}</option>
                        <option value="60">{{ __('support.duration_minutes', ['minutes' => 60]) }}</option>
                        <option value="120">{{ __('support.duration_minutes', ['minutes' => 120]) }}</option>
                    </select>
                    @error('durationMinutes')
                        <p class="ui-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="reason" class="ui-label">{{ __('common.reason') }}</label>
                    <textarea id="reason" wire:model="reason" rows="5" class="ui-textarea"></textarea>
                    @error('reason')
                        <p class="ui-error">{{ $message }}</p>
                    @enderror
                </div>

                <x-ui.button type="submit" wire:loading.attr="disabled" class="w-full disabled:cursor-not-allowed disabled:opacity-70">
                    {{ __('support.start_button') }}
                </x-ui.button>
            </form>
        </section>

        <section class="ui-card overflow-hidden">
            <div class="ui-card-header">
                <p class="ui-body">{{ __('support.index_note') }}</p>
            </div>

            <div class="overflow-x-auto">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th class="px-5 py-3 font-medium">{{ __('common.tenant') }}</th>
                            <th class="px-5 py-3 font-medium">{{ __('common.actor') }}</th>
                            <th class="px-5 py-3 font-medium">{{ __('common.status') }}</th>
                            <th class="px-5 py-3 font-medium">{{ __('common.reason') }}</th>
                            <th class="px-5 py-3 font-medium">{{ __('common.expires') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($supportSessions as $supportSession)
                            <tr wire:key="support-session-{{ $supportSession->id }}">
                                <td>
                                    <div class="font-medium text-[var(--ui-text)]">{{ $supportSession->tenant?->name ?? __('common.deleted_tenant') }}</div>
                                    <div class="ui-caption mt-1 font-mono">{{ $supportSession->tenant?->slug }}</div>
                                </td>
                                <td class="text-[var(--ui-text-muted)]">{{ $supportSession->actor?->email }}</td>
                                <td class="text-[var(--ui-text-muted)]">{{ $supportSession->status->value }}</td>
                                <td class="text-[var(--ui-text-muted)]">{{ $supportSession->reason }}</td>
                                <td class="text-[var(--ui-text-muted)]">{{ $supportSession->expires_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-10 text-center text-[var(--ui-text-muted)]">{{ __('support.empty') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($supportSessions->hasPages())
                <div class="ui-divider border-t px-5 py-4">
                    {{ $supportSessions->links() }}
                </div>
            @endif
        </section>
    </div>
</div>
