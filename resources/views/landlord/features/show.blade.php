@extends('landlord.layout')

@section('title', $feature->name.' | Aegoryx Admin')
@section('heading', $feature->name)
@section('subheading', 'Feature registry details and manual tenant overrides.')

@section('content')
    @if (session('success'))
        <div class="mb-5 rounded border border-emerald-800 bg-emerald-950 px-4 py-3 text-sm text-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-5 lg:grid-cols-[1fr_360px]">
        <section class="rounded border border-neutral-800 bg-neutral-900 p-5">
            <h2 class="text-lg font-semibold">Feature information</h2>

            <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-xs uppercase tracking-wide text-neutral-500">Key</dt>
                    <dd class="mt-1 font-mono text-sm text-neutral-100">{{ $feature->key }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-neutral-500">Status</dt>
                    <dd class="mt-1 text-neutral-100">{{ $feature->status->value }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs uppercase tracking-wide text-neutral-500">Description</dt>
                    <dd class="mt-1 text-neutral-100">{{ $feature->description ?: 'No description.' }}</dd>
                </div>
            </dl>

            <div class="mt-6 overflow-hidden rounded border border-neutral-800">
                <table class="min-w-full divide-y divide-neutral-800 text-sm">
                    <thead class="bg-neutral-950 text-left text-neutral-400">
                        <tr>
                            <th class="px-5 py-3 font-medium">Tenant</th>
                            <th class="px-5 py-3 font-medium">Enabled</th>
                            <th class="px-5 py-3 font-medium">Reason</th>
                            <th class="px-5 py-3 font-medium">Updated</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-800">
                        @forelse ($feature->tenantFeatures as $override)
                            <tr>
                                <td class="px-5 py-4">
                                    <div class="font-medium text-neutral-100">{{ $override->tenant?->name ?? 'Deleted tenant' }}</div>
                                    <div class="mt-1 font-mono text-xs text-neutral-500">{{ $override->tenant?->slug }}</div>
                                </td>
                                <td class="px-5 py-4 text-neutral-300">{{ $override->enabled ? 'yes' : 'no' }}</td>
                                <td class="px-5 py-4 text-neutral-300">{{ $override->reason }}</td>
                                <td class="px-5 py-4 text-neutral-400">{{ $override->updated_at?->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-10 text-center text-neutral-400">
                                    No manual overrides for this feature.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <aside class="space-y-5">
            <section class="rounded border border-neutral-800 bg-neutral-900 p-5">
                <h2 class="text-lg font-semibold">Global status</h2>

                <form method="POST" action="{{ route('landlord.features.status.update', $feature) }}" class="mt-4 space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="status" class="block text-sm font-medium text-neutral-300">Feature status</label>
                        <select
                            id="status"
                            name="status"
                            class="mt-2 block w-full rounded border border-neutral-700 bg-neutral-950 px-3 py-2 text-neutral-100 outline-none focus:border-sky-400"
                        >
                            <option value="active" @selected($feature->status->value === 'active')>active</option>
                            <option value="disabled" @selected($feature->status->value === 'disabled')>disabled</option>
                        </select>
                        @error('status')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full rounded bg-sky-500 px-4 py-2 font-medium text-white hover:bg-sky-400">
                        Update status
                    </button>
                </form>
            </section>

            <section class="rounded border border-neutral-800 bg-neutral-900 p-5">
                <h2 class="text-lg font-semibold">Tenant override</h2>

                <form method="POST" action="{{ route('landlord.features.tenant-overrides.store', $feature) }}" class="mt-4 space-y-4">
                    @csrf

                    <div>
                        <label for="tenant_id" class="block text-sm font-medium text-neutral-300">Tenant</label>
                        <select
                            id="tenant_id"
                            name="tenant_id"
                            class="mt-2 block w-full rounded border border-neutral-700 bg-neutral-950 px-3 py-2 text-neutral-100 outline-none focus:border-sky-400"
                        >
                            @foreach ($tenants as $tenant)
                                <option value="{{ $tenant->id }}" @selected((int) old('tenant_id') === $tenant->id)>
                                    {{ $tenant->name }} ({{ $tenant->slug }})
                                </option>
                            @endforeach
                        </select>
                        @error('tenant_id')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="enabled" class="block text-sm font-medium text-neutral-300">Override value</label>
                        <select
                            id="enabled"
                            name="enabled"
                            class="mt-2 block w-full rounded border border-neutral-700 bg-neutral-950 px-3 py-2 text-neutral-100 outline-none focus:border-sky-400"
                        >
                            <option value="1" @selected(old('enabled', '1') === '1')>enabled</option>
                            <option value="0" @selected(old('enabled') === '0')>disabled</option>
                        </select>
                        @error('enabled')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="reason" class="block text-sm font-medium text-neutral-300">Reason</label>
                        <textarea
                            id="reason"
                            name="reason"
                            rows="4"
                            class="mt-2 block w-full rounded border border-neutral-700 bg-neutral-950 px-3 py-2 text-neutral-100 outline-none focus:border-sky-400"
                        >{{ old('reason') }}</textarea>
                        @error('reason')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full rounded bg-sky-500 px-4 py-2 font-medium text-white hover:bg-sky-400">
                        Save override
                    </button>
                </form>
            </section>
        </aside>
    </div>
@endsection
