<section class="overflow-hidden rounded border border-neutral-800 bg-neutral-900">
    <div class="border-b border-neutral-800 px-5 py-4">
        <p class="text-sm text-neutral-400">License keys are stored and displayed only as hashes. Verification output is audited without secrets.</p>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-neutral-800 text-sm">
            <thead class="bg-neutral-950 text-left text-neutral-400">
                <tr>
                    <th class="px-5 py-3 font-medium">Tenant</th>
                    <th class="px-5 py-3 font-medium">Type</th>
                    <th class="px-5 py-3 font-medium">Status</th>
                    <th class="px-5 py-3 font-medium">Expires</th>
                    <th class="px-5 py-3 font-medium">Last verified</th>
                    <th class="px-5 py-3 font-medium"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-800">
                @forelse ($licenses as $license)
                    <tr wire:key="license-{{ $license->id }}">
                        <td class="px-5 py-4">
                            <div class="font-medium text-neutral-100">{{ $license->tenant?->name ?? 'Unassigned' }}</div>
                            <div class="mt-1 font-mono text-xs text-neutral-500">{{ $license->tenant?->slug }}</div>
                        </td>
                        <td class="px-5 py-4 text-neutral-300">{{ $license->type }}</td>
                        <td class="px-5 py-4 text-neutral-300">{{ $license->status->value }}</td>
                        <td class="px-5 py-4 text-neutral-400">{{ $license->expires_at?->format('Y-m-d') ?? 'perpetual' }}</td>
                        <td class="px-5 py-4 text-neutral-400">{{ $license->last_verified_at?->format('Y-m-d H:i') ?? 'never' }}</td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('landlord.licenses.show', $license) }}" wire:navigate class="text-sky-300 hover:text-sky-200">Open</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-neutral-400">No licenses yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($licenses->hasPages())
        <div class="border-t border-neutral-800 px-5 py-4">
            {{ $licenses->links() }}
        </div>
    @endif
</section>
