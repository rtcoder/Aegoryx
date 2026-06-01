<?php

namespace Tests\Feature\Tenancy;

use App\Models\Landlord\Tenant;
use App\Modules\Tenancy\Actions\CreateTenantAction;
use App\Modules\Tenancy\Enums\TenantBillingModel;
use App\Modules\Tenancy\Enums\TenantDeploymentType;
use App\Modules\Tenancy\Enums\TenantLicenseType;
use App\Modules\Tenancy\Enums\TenantStatus;
use App\Support\Localization\Locale;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

final class TenantCreationFlowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh', [
            '--database' => 'sqlite',
            '--path' => 'database/migrations/landlord',
        ]);
    }

    public function test_action_creates_tenant_with_safe_schema_name(): void
    {
        $tenant = $this->app->make(CreateTenantAction::class)->handle(
            name: 'Acme Labs',
            slug: 'raw-safe-slug',
            locale: Locale::German,
            migrate: false,
        );

        $this->assertSame('Acme Labs', $tenant->name);
        $this->assertSame('raw-safe-slug', $tenant->slug);
        $this->assertSame("tenant_{$tenant->id}", $tenant->schema_name);
        $this->assertSame(TenantStatus::Active, $tenant->status);
        $this->assertSame(Locale::German, $tenant->locale);
        $this->assertSame(TenantDeploymentType::Saas, $tenant->deployment_type);
        $this->assertSame(TenantBillingModel::Subscription, $tenant->billing_model);
        $this->assertSame(TenantLicenseType::SaasSubscription, $tenant->license_type);
    }

    public function test_action_generates_unique_slug_and_schema_names(): void
    {
        $createTenant = $this->app->make(CreateTenantAction::class);

        $first = $createTenant->handle('Acme Labs', migrate: false);
        $second = $createTenant->handle('Acme Labs', migrate: false);

        $this->assertSame('acme-labs', $first->slug);
        $this->assertSame('acme-labs-2', $second->slug);
        $this->assertSame("tenant_{$first->id}", $first->schema_name);
        $this->assertSame("tenant_{$second->id}", $second->schema_name);
    }

    public function test_command_creates_tenant(): void
    {
        $exitCode = Artisan::call('tenants:create', [
            'name' => 'Command Tenant',
            '--slug' => 'command-tenant',
            '--locale' => Locale::French->value,
            '--skip-migrations' => true,
        ]);

        $tenant = Tenant::query()->where('slug', 'command-tenant')->firstOrFail();

        $this->assertSame(0, $exitCode);
        $this->assertSame(Locale::French, $tenant->locale);
        $this->assertSame("tenant_{$tenant->id}", $tenant->schema_name);
    }
}
