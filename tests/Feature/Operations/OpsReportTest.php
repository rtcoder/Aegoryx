<?php

namespace Tests\Feature\Operations;

use App\Models\Landlord\BillingEvent;
use App\Models\Landlord\Identity;
use App\Models\Landlord\Tenant;
use App\Models\Landlord\TenantDomain;
use App\Modules\Billing\Enums\BillingEventStatus;
use App\Modules\Identity\Enums\IdentityStatus;
use App\Modules\Tenancy\Enums\TenantBillingModel;
use App\Modules\Tenancy\Enums\TenantDeploymentType;
use App\Modules\Tenancy\Enums\TenantDomainStatus;
use App\Modules\Tenancy\Enums\TenantDomainType;
use App\Modules\Tenancy\Enums\TenantLicenseType;
use App\Modules\Tenancy\Enums\TenantStatus;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

final class OpsReportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh', [
            '--database' => 'sqlite',
            '--path' => 'database/migrations/landlord',
        ]);
    }

    public function test_ops_report_outputs_json_snapshot(): void
    {
        $activeTenant = $this->tenant('acme', TenantStatus::Active);
        $this->tenant('paused', TenantStatus::Suspended);
        $this->domain($activeTenant, 'acme.aegoryx.test', TenantDomainStatus::Verified);
        $this->domain($activeTenant, 'pending.aegoryx.test', TenantDomainStatus::Pending);

        Identity::query()->create([
            'email' => 'root@example.test',
            'password' => 'secret-password',
            'is_super_admin' => true,
            'status' => IdentityStatus::Active,
        ]);

        BillingEvent::query()->create([
            'provider' => 'paddle',
            'provider_event_id' => 'evt_ops_failed',
            'event_type' => 'subscription.updated',
            'status' => BillingEventStatus::Failed,
            'payload' => ['status' => 'past_due'],
            'failure_reason' => 'Missing plan mapping.',
            'failed_at' => now(),
        ]);

        $exitCode = Artisan::call('aegoryx:ops-report', ['--json' => true]);

        $this->assertSame(0, $exitCode);
        $this->assertSame([
            'tenants_total' => 2,
            'tenants_active' => 1,
            'tenants_suspended' => 1,
            'domains_pending' => 1,
            'domains_verified' => 1,
            'superadmins_active' => 1,
            'billing_events_failed' => 1,
        ], json_decode(Artisan::output(), true));
    }

    private function tenant(string $slug, TenantStatus $status): Tenant
    {
        return Tenant::query()->create([
            'name' => ucfirst($slug),
            'slug' => $slug,
            'schema_name' => "tenant_{$slug}",
            'status' => $status,
            'deployment_type' => TenantDeploymentType::Saas,
            'billing_model' => TenantBillingModel::Subscription,
            'license_type' => TenantLicenseType::SaasSubscription,
        ]);
    }

    private function domain(Tenant $tenant, string $domain, TenantDomainStatus $status): TenantDomain
    {
        return TenantDomain::query()->create([
            'tenant_id' => $tenant->id,
            'domain' => $domain,
            'type' => TenantDomainType::Primary,
            'status' => $status,
        ]);
    }
}
