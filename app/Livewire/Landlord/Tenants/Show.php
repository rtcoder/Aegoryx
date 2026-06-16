<?php

namespace App\Livewire\Landlord\Tenants;

use App\Models\Landlord\Tenant;
use App\Modules\AdminConsole\Actions\UpdateTenantStatusAction;
use App\Modules\Entitlements\Actions\SetTenantFeatureOverrideAction;
use App\Modules\Entitlements\Enums\SystemFeature;
use App\Modules\Tenancy\Enums\TenantStatus;
use Illuminate\Validation\Rule;
use Livewire\Component;

final class Show extends Component
{
    public Tenant $tenant;

    public string $status;

    /**
     * @var array<string, bool>
     */
    public array $features = [];

    public string $featureReason = '';

    public function mount(Tenant $tenant): void
    {
        $this->tenant = $tenant->load('features');
        $this->status = $tenant->status->value;
        $this->featureReason = __('features.default_override_reason');
        $this->features = collect(SystemFeature::cases())
            ->mapWithKeys(fn (SystemFeature $feature): array => [
                $feature->value => $this->tenant->features
                    ->first(fn ($override): bool => $override->feature === $feature)
                    ?->enabled ?? false,
            ])
            ->all();
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

    public function saveFeatures(SetTenantFeatureOverrideAction $action): void
    {
        $this->validate([
            'features' => ['array'],
            'features.*' => ['boolean'],
            'featureReason' => ['required', 'string', 'max:1000'],
        ]);

        foreach (SystemFeature::cases() as $feature) {
            $action->handle(
                tenant: $this->tenant,
                feature: $feature,
                enabled: (bool) ($this->features[$feature->value] ?? false),
                reason: $this->featureReason,
                actor: auth('landlord')->user(),
                ip: request()->ip(),
                userAgent: request()->userAgent(),
            );
        }

        $this->tenant = $this->tenant->refresh()->load('features');

        session()->flash('success', __('flash.tenant_feature_override_saved'));
    }

    public function render()
    {
        return view('livewire.landlord.tenants.show', [
            'systemFeatures' => SystemFeature::cases(),
        ]);
    }
}
