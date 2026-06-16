<?php

namespace App\Modules\TenantPanel\Navigation;

use App\Models\Landlord\Tenant;
use App\Modules\Entitlements\Enums\SystemFeature;
use App\Modules\Entitlements\Services\EffectiveEntitlements;

final readonly class TenantNavigation
{
    public function __construct(
        private EffectiveEntitlements $entitlements,
    ) {}

    /**
     * @return array<int, array{label: string, route: string, description: string, feature_key: string|null}>
     */
    public function entries(): array
    {
        return [
            [
                'label' => __('tenant_panel.nav.dashboard'),
                'route' => 'tenant.dashboard',
                'description' => __('tenant_panel.nav.dashboard_description'),
                'feature_key' => null,
            ],
            [
                'label' => __('tenant_panel.nav.cms'),
                'route' => 'tenant.cms.index',
                'description' => __('tenant_panel.nav.cms_description'),
                'feature_key' => SystemFeature::Cms->value,
            ],
            [
                'label' => __('tenant_panel.nav.crm'),
                'route' => 'tenant.crm.index',
                'description' => __('tenant_panel.nav.crm_description'),
                'feature_key' => SystemFeature::Crm->value,
            ],
            [
                'label' => __('tenant_panel.nav.files'),
                'route' => 'tenant.files.index',
                'description' => __('tenant_panel.nav.files_description'),
                'feature_key' => SystemFeature::Files->value,
            ],
            [
                'label' => __('tenant_panel.nav.settings'),
                'route' => 'tenant.settings.index',
                'description' => __('tenant_panel.nav.settings_description'),
                'feature_key' => null,
            ],
        ];
    }

    /**
     * @return array<int, array{label: string, route: string, description: string, feature_key: string|null}>
     */
    public function visibleForTenant(Tenant $tenant): array
    {
        return array_values(array_filter(
            $this->entries(),
            fn (array $entry): bool => $entry['feature_key'] === null
                || $this->entitlements->allows($tenant, $entry['feature_key']),
        ));
    }

    /**
     * @return array<int, array{label: string, route: string, description: string, feature_key: string|null, enabled: bool}>
     */
    public function moduleCardsForTenant(Tenant $tenant): array
    {
        return array_values(array_map(
            fn (array $entry): array => [
                ...$entry,
                'enabled' => $entry['feature_key'] === null
                    || $this->entitlements->allows($tenant, $entry['feature_key']),
            ],
            array_filter($this->entries(), fn (array $entry): bool => $entry['route'] !== 'tenant.dashboard'),
        ));
    }
}
