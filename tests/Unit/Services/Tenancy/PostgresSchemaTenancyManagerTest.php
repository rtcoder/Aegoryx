<?php

namespace Tests\Unit\Services\Tenancy;

use App\Models\Landlord\Tenant;
use App\Modules\Tenancy\Enums\TenantBillingModel;
use App\Modules\Tenancy\Enums\TenantDeploymentType;
use App\Modules\Tenancy\Enums\TenantLicenseType;
use App\Modules\Tenancy\Enums\TenantStatus;
use App\Services\Tenancy\PostgresSchemaTenancyManager;
use Tests\TestCase;

final class PostgresSchemaTenancyManagerTest extends TestCase
{
    public function test_initialize_and_end_track_current_tenant(): void
    {
        $tenant = new Tenant([
            'name' => 'Acme',
            'slug' => 'acme',
            'schema_name' => 'tenant_1',
            'status' => TenantStatus::Active,
            'deployment_type' => TenantDeploymentType::Saas,
            'billing_model' => TenantBillingModel::Subscription,
            'license_type' => TenantLicenseType::SaasSubscription,
        ]);

        $manager = new PostgresSchemaTenancyManager;

        $manager->initialize($tenant);

        $this->assertSame($tenant, $manager->current());

        $manager->end();

        $this->assertNull($manager->current());
    }
}
