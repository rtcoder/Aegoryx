<section class="space-y-5">
    @if (session('success'))
        <div class="rounded border border-emerald-700 bg-emerald-950 px-4 py-3 text-sm text-emerald-100">
            {{ session('success') }}
        </div>
    @endif

    @if ($generatedPassword)
        <div class="rounded border border-sky-700 bg-sky-950 px-4 py-3 text-sm text-sky-100">
            <p class="font-medium">{{ __('tenant_panel.users.generated_password') }}</p>
            <p class="mt-2 font-mono">{{ $generatedPassword }}</p>
        </div>
    @endif

    @if ($canManageUsers)
        <form wire:submit="createUser" class="ui-card space-y-4">
            <div class="ui-card-header">
                <h2 class="ui-heading-2">{{ __('tenant_panel.users.create_title') }}</h2>
                <p class="ui-body mt-1">{{ __('tenant_panel.users.create_description') }}</p>
            </div>
            <div class="ui-card-body grid gap-4 lg:grid-cols-2">
                <div>
                    <label for="tenant_user_name" class="ui-label">{{ __('tenant_panel.users.name') }}</label>
                    <input id="tenant_user_name" wire:model="name" class="ui-input mt-2">
                    @error('name') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="tenant_user_email" class="ui-label">{{ __('tenant_panel.users.email') }}</label>
                    <input id="tenant_user_email" wire:model="email" type="email" class="ui-input mt-2">
                    @error('email') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="tenant_user_role" class="ui-label">{{ __('tenant_panel.users.role') }}</label>
                    <select id="tenant_user_role" wire:model="newRole" class="ui-select mt-2">
                        @foreach ($roleOptions as $roleValue => $roleLabel)
                            <option value="{{ $roleValue }}">{{ $roleLabel }}</option>
                        @endforeach
                    </select>
                    @error('newRole') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="tenant_user_password" class="ui-label">{{ __('tenant_panel.users.password') }}</label>
                    <input id="tenant_user_password" wire:model="password" type="text" class="ui-input mt-2">
                    <p class="ui-help">{{ __('tenant_panel.users.password_help') }}</p>
                    @error('password') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div class="lg:col-span-2">
                    <x-ui.button type="submit">{{ __('tenant_panel.users.create') }}</x-ui.button>
                </div>
            </div>
        </form>
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
