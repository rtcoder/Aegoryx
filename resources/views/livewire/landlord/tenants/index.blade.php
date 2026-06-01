<section class="overflow-hidden rounded border border-neutral-800 bg-neutral-900">
    <div class="border-b border-neutral-800 px-5 py-4">
        <p class="text-sm text-neutral-400">Tenant creation is handled by the dedicated tenant creation flow.</p>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-neutral-800 text-sm">
            <thead class="bg-neutral-950 text-left text-neutral-400">
                <tr>
                    <th class="px-5 py-3 font-medium">Name</th>
                    <th class="px-5 py-3 font-medium">Slug</th>
                    <th class="px-5 py-3 font-medium">Schema</th>
                    <th class="px-5 py-3 font-medium">Status</th>
                    <th class="px-5 py-3 font-medium">Deployment</th>
                    <th class="px-5 py-3 font-medium"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-800">
                @forelse ($tenants as $tenant)
                    <tr wire:key="tenant-{{ $tenant->id }}">
                        <td class="px-5 py-4 font-medium text-neutral-100">{{ $tenant->name }}</td>
                        <td class="px-5 py-4 text-neutral-300">{{ $tenant->slug }}</td>
                        <td class="px-5 py-4 font-mono text-xs text-neutral-400">{{ $tenant->schema_name }}</td>
                        <td class="px-5 py-4 text-neutral-300">{{ $tenant->status->value }}</td>
                        <td class="px-5 py-4 text-neutral-300">{{ $tenant->deployment_type->value }}</td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('landlord.tenants.show', $tenant) }}" wire:navigate class="text-sky-300 hover:text-sky-200">
                                Open
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-neutral-400">
                            No tenants yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($tenants->hasPages())
        <div class="border-t border-neutral-800 px-5 py-4">
            {{ $tenants->links() }}
        </div>
    @endif
</section>
