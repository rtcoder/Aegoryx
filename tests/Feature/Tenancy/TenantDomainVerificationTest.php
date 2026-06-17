<?php

namespace Tests\Feature\Tenancy;

use App\Models\Landlord\Tenant;
use App\Models\Landlord\TenantDomain;
use App\Modules\Tenancy\Enums\TenantBillingModel;
use App\Modules\Tenancy\Enums\TenantDeploymentType;
use App\Modules\Tenancy\Enums\TenantDomainStatus;
use App\Modules\Tenancy\Enums\TenantDomainType;
use App\Modules\Tenancy\Enums\TenantLicenseType;
use App\Modules\Tenancy\Enums\TenantStatus;
use App\Modules\Tenancy\Services\DnsTxtResolver;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

final class TenantDomainVerificationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh', [
            '--database' => 'sqlite',
            '--path' => 'database/migrations/landlord',
        ]);
    }

    public function test_pending_domain_is_verified_when_txt_token_matches(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Acme Tenant',
            'slug' => 'acme',
            'schema_name' => 'tenant_acme',
            'status' => TenantStatus::Active,
            'deployment_type' => TenantDeploymentType::Saas,
            'billing_model' => TenantBillingModel::Subscription,
            'license_type' => TenantLicenseType::SaasSubscription,
        ]);

        $domain = TenantDomain::query()->create([
            'tenant_id' => $tenant->id,
            'domain' => 'portal.example.test',
            'type' => TenantDomainType::Alias,
            'status' => TenantDomainStatus::Pending,
            'verification_token' => 'aegoryx-token',
        ]);

        $this->app->instance(DnsTxtResolver::class, new class implements DnsTxtResolver
        {
            public function records(string $host): array
            {
                return $host === '_aegoryx-domain.portal.example.test' ? ['aegoryx-token'] : [];
            }
        });

        $exitCode = Artisan::call('tenant-domains:verify', [
            '--domain' => 'portal.example.test',
        ]);

        $this->assertSame(0, $exitCode);
        $this->assertSame(TenantDomainStatus::Verified, $domain->refresh()->status);
        $this->assertNotNull($domain->verified_at);
    }
}
