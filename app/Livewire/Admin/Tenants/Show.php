<?php

namespace App\Livewire\Admin\Tenants;

use App\Models\System\Tenant;
use App\Modules\AdminConsole\Actions\UpdateTenantStatusAction;
use App\Modules\Entitlements\Actions\ClearTenantFeatureOverrideAction;
use App\Modules\Entitlements\Actions\SetTenantFeatureOverrideAction;
use App\Modules\Entitlements\Enums\SystemFeature;
use App\Modules\Entitlements\Services\EffectiveEntitlements;
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

    /**
     * @var array<string, array{enabled: bool, source: string|null, reason: string|null, config: array<string, mixed>, limits: array<string, mixed>}>
     */
    public array $effectiveEntitlements = [];

    public string $featureReason = '';

    public function mount(Tenant $tenant): void
    {
        $this->tenant = $tenant->load('features');
        $this->status = $tenant->status->value;
        $this->featureReason = __('features.default_override_reason');
        $this->syncFeatureState();
    }

    public function updateStatus(UpdateTenantStatusAction $action): void
    {
        $this->validate([
            'status' => ['required', Rule::enum(TenantStatus::class)],
        ]);

        $this->tenant = $action->handle(
            tenant: $this->tenant,
            status: TenantStatus::from($this->status),
            actor: auth('admin')->user(),
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
            $enabled = (bool) ($this->features[$feature->value] ?? false);

            if ($enabled === (bool) ($this->effectiveEntitlements[$feature->value]['enabled'] ?? false)) {
                continue;
            }

            $action->handle(
                tenant: $this->tenant,
                feature: $feature,
                enabled: $enabled,
                reason: $this->featureReason,
                actor: auth('admin')->user(),
                ip: request()->ip(),
                userAgent: request()->userAgent(),
            );
        }

        $this->tenant = $this->tenant->refresh()->load('features');
        $this->syncFeatureState();

        session()->flash('success', __('flash.tenant_feature_override_saved'));
    }

    public function clearFeatureOverride(string $featureKey, ClearTenantFeatureOverrideAction $action): void
    {
        $feature = SystemFeature::from($featureKey);

        $action->handle(
            tenant: $this->tenant,
            feature: $feature,
            actor: auth('admin')->user(),
            ip: request()->ip(),
            userAgent: request()->userAgent(),
        );

        $this->tenant = $this->tenant->refresh()->load('features');
        $this->syncFeatureState();

        session()->flash('success', __('flash.tenant_feature_override_cleared'));
    }

    public function render()
    {
        return view('livewire.admin.tenants.show', [
            'systemFeatures' => SystemFeature::cases(),
        ]);
    }

    private function syncFeatureState(): void
    {
        $this->effectiveEntitlements = app(EffectiveEntitlements::class)->forTenant($this->tenant);
        $this->features = collect(SystemFeature::cases())
            ->mapWithKeys(fn (SystemFeature $feature): array => [
                $feature->value => (bool) ($this->effectiveEntitlements[$feature->value]['enabled'] ?? false),
            ])
            ->all();
    }
}
