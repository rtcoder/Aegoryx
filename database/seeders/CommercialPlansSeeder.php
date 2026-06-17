<?php

namespace Database\Seeders;

use App\Models\Landlord\Plan;
use App\Models\Landlord\PlanFeature;
use App\Modules\Billing\Enums\PlanStatus;
use App\Modules\Entitlements\Enums\SystemFeature;
use Illuminate\Database\Seeder;

final class CommercialPlansSeeder extends Seeder
{
    /**
     * @var array<string, array{name: string, sort_order: int, billing_interval: string, limits: array<string, mixed>, features: list<SystemFeature>, metadata?: array<string, mixed>}>
     */
    private array $plans = [
        'starter' => [
            'name' => 'Starter',
            'sort_order' => 10,
            'billing_interval' => 'monthly',
            'limits' => [
                'cms.pages' => 25,
                'crm.contacts' => 500,
                'files.storage_mb' => 1024,
            ],
            'features' => [SystemFeature::Cms, SystemFeature::Crm, SystemFeature::Files],
        ],
        'growth' => [
            'name' => 'Growth',
            'sort_order' => 20,
            'billing_interval' => 'monthly',
            'limits' => [
                'cms.pages' => 250,
                'crm.contacts' => 5000,
                'files.storage_mb' => 10240,
                'public_api.requests_per_minute' => 120,
            ],
            'features' => [SystemFeature::Cms, SystemFeature::Crm, SystemFeature::Files],
        ],
        'business' => [
            'name' => 'Business',
            'sort_order' => 30,
            'billing_interval' => 'yearly',
            'limits' => [
                'cms.pages' => 'unlimited',
                'crm.contacts' => 25000,
                'files.storage_mb' => 102400,
                'public_api.requests_per_minute' => 600,
            ],
            'features' => [SystemFeature::Cms, SystemFeature::Crm, SystemFeature::Files],
            'metadata' => ['priority_support' => true],
        ],
    ];

    public function run(): void
    {
        foreach ($this->plans as $key => $definition) {
            $plan = Plan::query()->updateOrCreate(
                ['key' => $key],
                [
                    'name' => $definition['name'],
                    'status' => PlanStatus::Active,
                    'billing_interval' => $definition['billing_interval'],
                    'sort_order' => $definition['sort_order'],
                    'limits' => $definition['limits'],
                    'metadata' => $definition['metadata'] ?? [],
                ],
            );

            foreach ($definition['features'] as $feature) {
                PlanFeature::query()->updateOrCreate(
                    [
                        'plan_id' => $plan->id,
                        'feature' => $feature,
                    ],
                    [
                        'enabled' => true,
                        'config' => [],
                    ],
                );
            }
        }
    }
}
