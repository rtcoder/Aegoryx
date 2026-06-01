<?php

namespace App\Livewire\Landlord\Features;

use App\Models\Landlord\Feature;
use App\Models\Landlord\Tenant;
use App\Modules\Entitlements\Actions\SetTenantFeatureOverrideAction;
use App\Modules\Entitlements\Actions\UpdateFeatureStatusAction;
use App\Modules\Entitlements\Enums\FeatureStatus;
use Illuminate\Validation\Rule;
use Livewire\Component;

final class Show extends Component
{
    public Feature $feature;

    public string $status;

    public ?int $tenantId = null;

    public string $enabled = '1';

    public string $reason = '';

    public function mount(Feature $feature): void
    {
        $this->feature = $feature;
        $this->status = $feature->status->value;
        $this->tenantId = Tenant::query()->orderBy('name')->value('id');
    }

    public function updateStatus(UpdateFeatureStatusAction $action): void
    {
        $this->validate([
            'status' => ['required', Rule::enum(FeatureStatus::class)],
        ]);

        $this->feature = $action->handle(
            feature: $this->feature,
            status: FeatureStatus::from($this->status),
            actor: auth('landlord')->user(),
            ip: request()->ip(),
            userAgent: request()->userAgent(),
        );

        session()->flash('success', __('flash.feature_status_updated'));
    }

    public function setTenantOverride(SetTenantFeatureOverrideAction $action): void
    {
        $this->validate([
            'tenantId' => ['required', 'integer', Rule::exists('tenants', 'id')],
            'enabled' => ['required', 'boolean'],
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $tenant = Tenant::query()->findOrFail($this->tenantId);

        $action->handle(
            tenant: $tenant,
            feature: $this->feature,
            enabled: (bool) $this->enabled,
            reason: $this->reason,
            actor: auth('landlord')->user(),
            ip: request()->ip(),
            userAgent: request()->userAgent(),
        );

        $this->reason = '';

        session()->flash('success', __('flash.tenant_feature_override_saved'));
    }

    public function render()
    {
        return view('livewire.landlord.features.show', [
            'feature' => $this->feature->load([
                'tenantFeatures' => fn ($query) => $query
                    ->where('source', 'manual')
                    ->with('tenant')
                    ->latest(),
            ]),
            'tenants' => Tenant::query()
                ->orderBy('name')
                ->get(['id', 'name', 'slug']),
        ]);
    }
}
