<?php

namespace App\Livewire\Tenant\Users;

use App\Models\Tenant\User;
use App\Modules\Identity\Actions\CreateTenantUserAction;
use App\Modules\Identity\Actions\UpdateTenantUserRoleAction;
use App\Modules\Identity\Enums\TenantUserRole;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

final class Index extends Component
{
    /**
     * @var array<int, string>
     */
    public array $roles = [];

    public string $name = '';

    public string $email = '';

    public string $newRole = 'member';

    public ?string $password = null;

    public ?string $generatedPassword = null;

    public function mount(): void
    {
        $this->roles = User::query()
            ->orderBy('name')
            ->pluck('role', 'id')
            ->map(fn (TenantUserRole $role): string => $role->value)
            ->all();
    }

    public function createUser(CreateTenantUserAction $action): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'newRole' => ['required', Rule::enum(TenantUserRole::class)],
            'password' => ['nullable', 'string', 'min:12'],
        ]);

        $actor = Auth::user();
        abort_unless($actor instanceof User, 403);

        $result = $action->handle(
            name: $validated['name'],
            email: $validated['email'],
            role: TenantUserRole::from($validated['newRole']),
            actor: $actor,
            password: $validated['password'] ?: null,
        );

        $this->generatedPassword = $result['password'];
        $this->reset(['name', 'email', 'newRole', 'password']);
        $this->newRole = TenantUserRole::Member->value;
        $this->mount();

        session()->flash('success', __('tenant_panel.users.created'));
    }

    public function updateRole(int $userId, UpdateTenantUserRoleAction $action): void
    {
        $this->validate([
            "roles.{$userId}" => ['required', Rule::enum(TenantUserRole::class)],
        ]);

        $actor = Auth::user();
        abort_unless($actor instanceof User, 403);

        $user = User::query()->findOrFail($userId);

        $action->handle(
            user: $user,
            role: TenantUserRole::from($this->roles[$userId]),
            actor: $actor,
        );

        session()->flash('success', __('tenant_panel.users.role_updated'));
    }

    public function render(): View
    {
        return view('livewire.tenant.users.index', [
            'canManageUsers' => Auth::user()?->canManageTenantUsers() === true,
            'roleOptions' => collect(TenantUserRole::cases())
                ->mapWithKeys(fn (TenantUserRole $role): array => [$role->value => __("tenant_panel.roles.{$role->value}")])
                ->all(),
            'users' => User::query()
                ->orderBy('name')
                ->get(),
        ]);
    }
}
