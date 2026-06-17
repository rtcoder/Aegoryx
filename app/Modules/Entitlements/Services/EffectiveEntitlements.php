<?php

namespace App\Modules\Entitlements\Services;

use App\Models\Landlord\License;
use App\Models\Landlord\PlanFeature;
use App\Models\Landlord\Subscription;
use App\Models\Landlord\Tenant;
use App\Models\Landlord\TenantFeature;
use App\Modules\Billing\Enums\PlanStatus;
use App\Modules\Billing\Enums\SubscriptionStatus;
use App\Modules\Entitlements\Enums\SystemFeature;
use App\Modules\Entitlements\Enums\TenantFeatureSource;
use App\Modules\Licensing\Enums\LicenseStatus;

final readonly class EffectiveEntitlements
{
    /**
     * @return array<string, array{enabled: bool, source: string|null, reason: string|null, config: array<string, mixed>, limits: array<string, mixed>}>
     */
    public function forTenant(Tenant $tenant): array
    {
        $effective = $this->emptyMatrix();

        $this->applySubscriptionPlan($effective, $tenant);
        $this->applyLicensePayload($effective, $tenant);
        $this->applyManualOverrides($effective, $tenant);

        return $effective;
    }

    public function allows(Tenant $tenant, string|SystemFeature $featureKey): bool
    {
        $featureKey = $featureKey instanceof SystemFeature ? $featureKey->value : $featureKey;

        return SystemFeature::tryFrom($featureKey) !== null
            && ($this->forTenant($tenant)[$featureKey]['enabled'] ?? false) === true;
    }

    public function limit(Tenant $tenant, string $limitKey, mixed $default = null): mixed
    {
        foreach ($this->forTenant($tenant) as $entitlement) {
            if (array_key_exists($limitKey, $entitlement['limits'])) {
                return $entitlement['limits'][$limitKey];
            }
        }

        return $default;
    }

    public function config(Tenant $tenant, string|SystemFeature $featureKey, string $configKey, mixed $default = null): mixed
    {
        $featureKey = $featureKey instanceof SystemFeature ? $featureKey->value : $featureKey;

        return $this->forTenant($tenant)[$featureKey]['config'][$configKey] ?? $default;
    }

    /**
     * @return array<string, array{enabled: bool, source: string|null, reason: string|null, config: array<string, mixed>, limits: array<string, mixed>}>
     */
    private function emptyMatrix(): array
    {
        return collect(SystemFeature::cases())
            ->mapWithKeys(fn (SystemFeature $feature): array => [
                $feature->value => [
                    'enabled' => false,
                    'source' => null,
                    'reason' => null,
                    'config' => [],
                    'limits' => [],
                ],
            ])
            ->all();
    }

    /**
     * @param  array<string, array{enabled: bool, source: string|null, reason: string|null, config: array<string, mixed>, limits: array<string, mixed>}>  $effective
     */
    private function applySubscriptionPlan(array &$effective, Tenant $tenant): void
    {
        $subscription = Subscription::query()
            ->with('plan.features')
            ->where('tenant_id', $tenant->id)
            ->whereIn('status', [
                SubscriptionStatus::Active->value,
                SubscriptionStatus::Trialing->value,
            ])
            ->latest('id')
            ->first();

        if (! $subscription?->plan || $subscription->plan->status !== PlanStatus::Active) {
            return;
        }

        $limits = $subscription->plan->limits ?? [];

        $subscription->plan->features->each(function (PlanFeature $planFeature) use (&$effective, $subscription, $limits): void {
            $featureKey = $planFeature->feature->value;

            if (! array_key_exists($featureKey, $effective)) {
                return;
            }

            $effective[$featureKey] = [
                'enabled' => $planFeature->enabled,
                'source' => TenantFeatureSource::Plan->value,
                'reason' => "plan:{$subscription->plan->key}",
                'config' => $planFeature->config ?? [],
                'limits' => $limits,
            ];
        });
    }

    /**
     * @param  array<string, array{enabled: bool, source: string|null, reason: string|null, config: array<string, mixed>, limits: array<string, mixed>}>  $effective
     */
    private function applyLicensePayload(array &$effective, Tenant $tenant): void
    {
        $license = License::query()
            ->where('tenant_id', $tenant->id)
            ->whereIn('status', [
                LicenseStatus::Active->value,
                LicenseStatus::Grace->value,
            ])
            ->latest('id')
            ->first();

        if (! $license) {
            return;
        }

        $features = $license->payload['features'] ?? [];
        $limits = $license->payload['limits'] ?? [];

        foreach ($features as $key => $definition) {
            $featureKey = is_int($key) ? $definition : $key;

            if (! is_string($featureKey) || ! array_key_exists($featureKey, $effective)) {
                continue;
            }

            $enabled = is_array($definition)
                ? ($definition['enabled'] ?? true)
                : (bool) $definition;

            $effective[$featureKey] = [
                'enabled' => $enabled,
                'source' => TenantFeatureSource::License->value,
                'reason' => "license:{$license->status->value}",
                'config' => is_array($definition) ? ($definition['config'] ?? []) : [],
                'limits' => is_array($limits) ? $limits : [],
            ];
        }
    }

    /**
     * @param  array<string, array{enabled: bool, source: string|null, reason: string|null, config: array<string, mixed>, limits: array<string, mixed>}>  $effective
     */
    private function applyManualOverrides(array &$effective, Tenant $tenant): void
    {
        $manualOverrides = TenantFeature::query()
            ->where('tenant_id', $tenant->id)
            ->where('source', TenantFeatureSource::Manual->value)
            ->get()
            ->keyBy(fn (TenantFeature $override): string => $override->feature->value);

        foreach (SystemFeature::cases() as $feature) {
            $override = $manualOverrides->get($feature->value);

            if (! $override) {
                continue;
            }

            $config = $override->config ?? [];

            $effective[$feature->value] = [
                'enabled' => $override->enabled,
                'source' => $override->source->value,
                'reason' => $override->reason,
                'config' => $config['config'] ?? [],
                'limits' => $config['limits'] ?? $effective[$feature->value]['limits'],
            ];
        }
    }
}
