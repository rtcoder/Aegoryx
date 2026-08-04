<section class="ui-card overflow-hidden">
    <div class="ui-card-header">
        <p class="ui-body">{{ __('licenses.index_note') }}</p>
    </div>

    <div class="overflow-x-auto">
        <table class="ui-table">
            <thead>
                <tr>
                    <th class="px-5 py-3 font-medium">{{ __('common.tenant') }}</th>
                    <th class="px-5 py-3 font-medium">{{ __('common.type') }}</th>
                    <th class="px-5 py-3 font-medium">{{ __('common.status') }}</th>
                    <th class="px-5 py-3 font-medium">{{ __('common.expires') }}</th>
                    <th class="px-5 py-3 font-medium">{{ __('common.last_verified') }}</th>
                    <th class="px-5 py-3 font-medium"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($licenses as $license)
                    <tr wire:key="license-{{ $license->id }}">
                        <td>
                            <div class="font-medium text-[var(--ui-text)]">{{ $license->tenant?->name ?? __('common.unassigned') }}</div>
                            <div class="ui-caption mt-1 font-mono">{{ $license->tenant?->slug }}</div>
                        </td>
                        <td class="text-[var(--ui-text-muted)]">{{ $license->type }}</td>
                        <td class="text-[var(--ui-text-muted)]">{{ $license->status->value }}</td>
                        <td class="text-[var(--ui-text-muted)]">{{ $license->expires_at?->format('Y-m-d') ?? __('common.perpetual') }}</td>
                        <td class="text-[var(--ui-text-muted)]">{{ $license->last_verified_at?->format('Y-m-d H:i') ?? __('common.never') }}</td>
                        <td class="text-right">
                            <a href="{{ route('admin.licenses.show', $license) }}" wire:navigate class="ui-link">{{ __('common.open') }}</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-10 text-center text-[var(--ui-text-muted)]">{{ __('licenses.empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($licenses->hasPages())
        <div class="ui-divider border-t px-5 py-4">
            {{ $licenses->links() }}
        </div>
    @endif
</section>
