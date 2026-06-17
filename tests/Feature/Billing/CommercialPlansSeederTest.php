<?php

namespace Tests\Feature\Billing;

use App\Models\Landlord\Plan;
use App\Models\Landlord\PlanFeature;
use App\Modules\Entitlements\Enums\SystemFeature;
use Database\Seeders\CommercialPlansSeeder;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

final class CommercialPlansSeederTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh', [
            '--database' => 'sqlite',
            '--path' => 'database/migrations/landlord',
        ]);
    }

    public function test_commercial_plan_defaults_are_seeded_idempotently(): void
    {
        $this->seed(CommercialPlansSeeder::class);
        $this->seed(CommercialPlansSeeder::class);

        $this->assertSame(3, Plan::query()->count());

        $starter = Plan::query()->where('key', 'starter')->firstOrFail();
        $business = Plan::query()->where('key', 'business')->firstOrFail();

        $this->assertSame(25, $starter->limits['cms.pages']);
        $this->assertSame('unlimited', $business->limits['cms.pages']);
        $this->assertSame(3, PlanFeature::query()->where('plan_id', $starter->id)->count());
        $this->assertDatabaseHas('plan_features', [
            'plan_id' => $starter->id,
            'feature' => SystemFeature::Crm->value,
            'enabled' => true,
        ]);
    }
}
