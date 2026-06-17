<section class="space-y-5">
    @if (session('success'))
        <div class="rounded border border-emerald-700 bg-emerald-950 px-4 py-3 text-sm text-emerald-100">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-hidden rounded border border-neutral-800">
        <table class="min-w-full divide-y divide-neutral-800 text-sm">
            <thead class="bg-neutral-900 text-left text-xs uppercase tracking-wide text-neutral-500">
                <tr>
                    <th class="px-4 py-3">{{ __('tenant_panel.users.name') }}</th>
                    <th class="px-4 py-3">{{ __('tenant_panel.users.email') }}</th>
                    <th class="px-4 py-3">{{ __('tenant_panel.users.role') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('tenant_panel.users.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-800 bg-neutral-950">
                @foreach ($users as $user)
                    <tr>
                        <td class="px-4 py-3 font-medium text-neutral-100">{{ $user->name }}</td>
                        <td class="px-4 py-3 text-neutral-400">{{ $user->email }}</td>
                        <td class="px-4 py-3">
                            @if ($canManageUsers)
                                <select wire:model="roles.{{ $user->id }}" class="ui-select max-w-48">
                                    @foreach ($roleOptions as $roleValue => $roleLabel)
                                        <option value="{{ $roleValue }}">{{ $roleLabel }}</option>
                                    @endforeach
                                </select>
                                @error("roles.{$user->id}") <p class="ui-error">{{ $message }}</p> @enderror
                            @else
                                <x-ui.badge>{{ __("tenant_panel.roles.{$user->role->value}") }}</x-ui.badge>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if ($canManageUsers)
                                <x-ui.button wire:click="updateRole({{ $user->id }})" size="sm" variant="secondary">
                                    {{ __('tenant_panel.users.save_role') }}
                                </x-ui.button>
                            @else
                                <span class="text-xs text-neutral-500">{{ __('tenant_panel.users.read_only') }}</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
