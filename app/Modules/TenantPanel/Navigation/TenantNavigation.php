<?php

namespace App\Modules\TenantPanel\Navigation;

use App\Models\Landlord\Tenant;
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
                'label' => 'Dashboard',
                'route' => 'tenant.dashboard',
                'description' => 'Workspace overview',
                'feature_key' => null,
            ],
            [
                'label' => 'CMS',
                'route' => 'tenant.cms.index',
                'description' => 'Pages, publishing, revisions',
                'feature_key' => 'cms',
            ],
            [
                'label' => 'CRM',
                'route' => 'tenant.crm.index',
                'description' => 'Contacts, companies, deals',
                'feature_key' => 'crm',
            ],
            [
                'label' => 'Files',
                'route' => 'tenant.files.index',
                'description' => 'Private storage and downloads',
                'feature_key' => 'files',
            ],
            [
                'label' => 'Settings',
                'route' => 'tenant.settings.index',
                'description' => 'Workspace configuration',
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
