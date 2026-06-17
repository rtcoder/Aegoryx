<?php

namespace Tests\Unit\Support\Queue;

use App\Models\Landlord\Tenant;
use App\Modules\Tenancy\Enums\TenantBillingModel;
use App\Modules\Tenancy\Enums\TenantDeploymentType;
use App\Modules\Tenancy\Enums\TenantLicenseType;
use App\Modules\Tenancy\Enums\TenantStatus;
use App\Services\Tenancy\TenancyManager;
use App\Support\Queue\InteractsWithTenantContext;
use Illuminate\Support\Facades\Artisan;
use LogicException;
use Tests\TestCase;

final class InteractsWithTenantContextTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh', [
            '--database' => 'sqlite',
            '--path' => 'database/migrations/landlord',
        ]);
    }

    public function test_tenant_aware_job_requires_tenant_id(): void
    {
        $job = new TenantAwareJobStub;

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Tenant-aware jobs must be dispatched with tenantId.');

        $job->handle(new RecordingTenancyManager);
    }

    public function test_tenant_aware_job_initializes_and_resets_context(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Acme',
            'slug' => 'acme',
            'schema_name' => 'tenant_acme',
            'status' => TenantStatus::Active,
            'deployment_type' => TenantDeploymentType::Saas,
            'billing_model' => TenantBillingModel::Subscription,
            'license_type' => TenantLicenseType::SaasSubscription,
        ]);
        $manager = new RecordingTenancyManager;

        $result = (new TenantAwareJobStub)
            ->forTenant($tenant)
            ->handle($manager);

        $this->assertSame($tenant->id, $result);
        $this->assertSame($tenant->id, $manager->lastInitializedTenantId);
        $this->assertTrue($manager->ended);
        $this->assertNull($manager->current());
        $this->assertSame(["tenant:{$tenant->id}"], (new TenantAwareJobStub)->forTenant($tenant)->tags());
    }
}

final class TenantAwareJobStub
{
    use InteractsWithTenantContext;

    public function handle(TenancyManager $tenancy): int
    {
        return $this->runWithTenantContext($tenancy, fn (Tenant $tenant): int => $tenant->id);
    }
}

final class RecordingTenancyManager implements TenancyManager
{
    public ?Tenant $initializedTenant = null;

    public ?int $lastInitializedTenantId = null;

    public bool $ended = false;

    public function initialize(Tenant $tenant): void
    {
        $this->initializedTenant = $tenant;
        $this->lastInitializedTenantId = $tenant->id;
        $this->ended = false;
    }

    public function end(): void
    {
        $this->ended = true;
        $this->initializedTenant = null;
    }

    public function current(): ?Tenant
    {
        return $this->initializedTenant;
    }
}
