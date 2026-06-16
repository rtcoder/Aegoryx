<?php

namespace App\Modules\Entitlements\Services;

use App\Models\Landlord\Tenant;
use App\Models\Landlord\TenantFeature;
use App\Modules\Entitlements\Enums\SystemFeature;
use App\Modules\Entitlements\Enums\TenantFeatureSource;

final readonly class EffectiveEntitlements
{
    /**
     * @return array<string, array{enabled: bool, source: string|null, reason: string|null, limits: array<string, mixed>}>
     */
    public function forTenant(Tenant $tenant): array
    {
        $manualOverrides = TenantFeature::query()
            ->where('tenant_id', $tenant->id)
            ->where('source', TenantFeatureSource::Manual->value)
            ->get()
            ->keyBy(fn (TenantFeature $override): string => $override->feature->value);

        return collect(SystemFeature::cases())
            ->mapWithKeys(function (SystemFeature $feature) use ($manualOverrides): array {
                $override = $manualOverrides->get($feature->value);

                return [
                    $feature->value => [
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
        return SystemFeature::tryFrom($featureKey) !== null
            && ($this->forTenant($tenant)[$featureKey]['enabled'] ?? false) === true;
    }
}
