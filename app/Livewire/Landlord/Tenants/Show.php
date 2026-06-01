<?php

namespace App\Livewire\Landlord\Tenants;

use App\Models\Landlord\Tenant;
use App\Modules\AdminConsole\Actions\UpdateTenantStatusAction;
use App\Modules\Tenancy\Enums\TenantStatus;
use Illuminate\Validation\Rule;
use Livewire\Component;

final class Show extends Component
{
    public Tenant $tenant;

    public string $status;

    public function mount(Tenant $tenant): void
    {
        $this->tenant = $tenant;
        $this->status = $tenant->status->value;
    }

    public function updateStatus(UpdateTenantStatusAction $action): void
    {
        $this->validate([
            'status' => ['required', Rule::enum(TenantStatus::class)],
        ]);

        $this->tenant = $action->handle(
            tenant: $this->tenant,
            status: TenantStatus::from($this->status),
            actor: auth('landlord')->user(),
            ip: request()->ip(),
            userAgent: request()->userAgent(),
        );

        session()->flash('success', __('flash.tenant_status_updated'));
    }

    public function render()
    {
        return view('livewire.landlord.tenants.show');
    }
}
