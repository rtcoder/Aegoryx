<div class="grid gap-5 lg:grid-cols-[360px_1fr]">
    <section class="rounded border border-neutral-800 bg-neutral-900 p-5">
        <h2 class="text-lg font-semibold">Create feature</h2>

        <form wire:submit="createFeature" class="mt-5 space-y-4">
            <div>
                <label for="key" class="block text-sm font-medium text-neutral-300">Key</label>
                <input id="key" wire:model="key" placeholder="cms.pages" class="mt-2 block w-full rounded border border-neutral-700 bg-neutral-950 px-3 py-2 text-neutral-100 outline-none focus:border-sky-400">
                @error('key') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="name" class="block text-sm font-medium text-neutral-300">Name</label>
                <input id="name" wire:model="name" placeholder="CMS Pages" class="mt-2 block w-full rounded border border-neutral-700 bg-neutral-950 px-3 py-2 text-neutral-100 outline-none focus:border-sky-400">
                @error('name') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-neutral-300">Status</label>
                <select id="status" wire:model="status" class="mt-2 block w-full rounded border border-neutral-700 bg-neutral-950 px-3 py-2 text-neutral-100 outline-none focus:border-sky-400">
                    <option value="active">active</option>
                    <option value="disabled">disabled</option>
                </select>
                @error('status') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-neutral-300">Description</label>
                <textarea id="description" wire:model="description" rows="4" class="mt-2 block w-full rounded border border-neutral-700 bg-neutral-950 px-3 py-2 text-neutral-100 outline-none focus:border-sky-400"></textarea>
                @error('description') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>

            <button type="submit" wire:loading.attr="disabled" class="w-full rounded bg-sky-500 px-4 py-2 font-medium text-white hover:bg-sky-400 disabled:cursor-not-allowed disabled:opacity-70">
                Create feature
            </button>
        </form>
    </section>

    <section class="overflow-hidden rounded border border-neutral-800 bg-neutral-900">
        <div class="border-b border-neutral-800 px-5 py-4">
            <p class="text-sm text-neutral-400">Business modules should resolve access through Entitlements, not by reading admin tables directly.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-neutral-800 text-sm">
                <thead class="bg-neutral-950 text-left text-neutral-400">
                    <tr>
                        <th class="px-5 py-3 font-medium">Key</th>
                        <th class="px-5 py-3 font-medium">Name</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                        <th class="px-5 py-3 font-medium">Overrides</th>
                        <th class="px-5 py-3 font-medium"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-800">
                    @forelse ($features as $feature)
                        <tr wire:key="feature-{{ $feature->id }}">
                            <td class="px-5 py-4 font-mono text-xs text-neutral-300">{{ $feature->key }}</td>
                            <td class="px-5 py-4 font-medium text-neutral-100">{{ $feature->name }}</td>
                            <td class="px-5 py-4 text-neutral-300">{{ $feature->status->value }}</td>
                            <td class="px-5 py-4 text-neutral-300">{{ $feature->tenant_features_count }}</td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('landlord.features.show', $feature) }}" wire:navigate class="text-sky-300 hover:text-sky-200">Open</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-neutral-400">No features yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($features->hasPages())
            <div class="border-t border-neutral-800 px-5 py-4">
                {{ $features->links() }}
            </div>
        @endif
    </section>
</div>
