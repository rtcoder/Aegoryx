<section class="ui-card overflow-hidden">
    <div class="ui-card-header">
        <p class="ui-body">{{ __('tenants.index_note') }}</p>
    </div>

    <div class="overflow-x-auto">
        <table class="ui-table">
            <thead>
                <tr>
                    <th class="px-5 py-3 font-medium">{{ __('common.name') }}</th>
                    <th class="px-5 py-3 font-medium">{{ __('common.slug') }}</th>
                    <th class="px-5 py-3 font-medium">{{ __('common.schema') }}</th>
                    <th class="px-5 py-3 font-medium">{{ __('common.status') }}</th>
                    <th class="px-5 py-3 font-medium">{{ __('common.deployment') }}</th>
                    <th class="px-5 py-3 font-medium"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tenants as $tenant)
                    <tr wire:key="tenant-{{ $tenant->id }}">
                        <td class="font-medium text-[var(--ui-text)]">{{ $tenant->name }}</td>
                        <td class="text-[var(--ui-text-muted)]">{{ $tenant->slug }}</td>
                        <td class="ui-caption font-mono">{{ $tenant->schema_name }}</td>
                        <td class="text-[var(--ui-text-muted)]">{{ $tenant->status->value }}</td>
                        <td class="text-[var(--ui-text-muted)]">{{ $tenant->deployment_type->value }}</td>
                        <td class="text-right">
                            <a href="{{ route('admin.tenants.show', $tenant) }}" wire:navigate class="ui-link">
                                {{ __('common.open') }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-10 text-center text-[var(--ui-text-muted)]">
                            {{ __('tenants.empty') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($tenants->hasPages())
        <div class="ui-divider border-t px-5 py-4">
            {{ $tenants->links() }}
        </div>
    @endif
</section>
