<?php

namespace App\Modules\Entitlements\Services;

use App\Models\Landlord\Feature;
use App\Models\Landlord\Tenant;
use App\Models\Landlord\TenantFeature;
use App\Modules\Entitlements\Enums\FeatureStatus;
use App\Modules\Entitlements\Enums\TenantFeatureSource;

final readonly class EffectiveEntitlements
{
    /**
     * @return array<string, array{enabled: bool, source: string|null, reason: string|null, limits: array<string, mixed>}>
     */
    public function forTenant(Tenant $tenant): array
    {
        $features = Feature::query()
            ->where('status', FeatureStatus::Active->value)
            ->get()
            ->keyBy('key');

        $manualOverrides = TenantFeature::query()
            ->where('tenant_id', $tenant->id)
            ->where('source', TenantFeatureSource::Manual->value)
            ->with('feature')
            ->get()
            ->keyBy(fn (TenantFeature $override): string => $override->feature?->key ?? '');

        return $features
            ->mapWithKeys(function (Feature $feature) use ($manualOverrides): array {
                $override = $manualOverrides->get($feature->key);

                return [
                    $feature->key => [
                        'enabled' => $override?->enabled ?? false,
                        'source' => $override?->source->value,
                        'reason' => $override?->reason,
                        'limits' => $override?->config ?? [],
                    ],
                ];
            })
            ->all();
    }

    public function allows(Tenant $tenant, string $featureKey): bool
    {
        return ($this->forTenant($tenant)[$featureKey]['enabled'] ?? false) === true;
    }
}
