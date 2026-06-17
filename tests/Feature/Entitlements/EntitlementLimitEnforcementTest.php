<?php

namespace Tests\Feature\Entitlements;

use App\Livewire\Tenant\Cms\Pages\Index as CmsPagesIndex;
use App\Models\Landlord\Tenant;
use App\Models\Landlord\TenantDomain;
use App\Models\Landlord\TenantFeature;
use App\Models\Tenant\CmsPage;
use App\Models\Tenant\CrmContact;
use App\Models\Tenant\TenantFile;
use App\Models\Tenant\User;
use App\Modules\Cms\Enums\CmsPageStatus;
use App\Modules\Entitlements\Enums\TenantFeatureSource;
use App\Modules\Tenancy\Enums\TenantBillingModel;
use App\Modules\Tenancy\Enums\TenantDeploymentType;
use App\Modules\Tenancy\Enums\TenantDomainStatus;
use App\Modules\Tenancy\Enums\TenantDomainType;
use App\Modules\Tenancy\Enums\TenantLicenseType;
use App\Modules\Tenancy\Enums\TenantStatus;
use App\Services\Tenancy\TenancyManager;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

final class EntitlementLimitEnforcementTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh', [
            '--database' => 'sqlite',
            '--path' => 'database/migrations/landlord',
        ]);

        Artisan::call('migrate', [
            '--database' => 'sqlite',
            '--path' => 'database/migrations/tenant',
        ]);

        Storage::fake('local');
    }

    public function test_cms_page_limit_is_enforced(): void
    {
        $tenant = $this->tenant();
        $this->domain($tenant);
        $this->manualOverride($tenant, 'cms', true, ['limits' => ['cms.pages' => 1]]);
        $this->actingAs($this->user(), 'web');
        app(TenancyManager::class)->initialize($tenant);

        CmsPage::query()->create([
            'title' => 'Existing',
            'slug' => 'existing',
            'status' => CmsPageStatus::Draft,
            'draft_content' => ['blocks' => []],
        ]);

        Livewire::test(CmsPagesIndex::class)
            ->set('title', 'New page')
            ->set('body', 'Body')
            ->call('save')
            ->assertHasErrors('limit');

        app(TenancyManager::class)->end();
    }

    public function test_crm_contact_limit_is_enforced(): void
    {
        $tenant = $this->tenant();
        $this->domain($tenant);
        $this->manualOverride($tenant, 'crm', true, ['limits' => ['crm.contacts' => 1]]);
        $this->actingAs($this->user(), 'web');

        CrmContact::query()->create(['first_name' => 'Existing']);

        $this
            ->post('http://acme.aegoryx.test/panel/crm/contacts', [
                'first_name' => 'Ada',
            ])
            ->assertSessionHasErrors('limit');
    }

    public function test_file_storage_limit_is_enforced(): void
    {
        $tenant = $this->tenant();
        $this->domain($tenant);
        $this->manualOverride($tenant, 'files', true, ['limits' => ['files.storage_mb' => 0]]);
        $this->actingAs($this->user(), 'web');

        $this
            ->post('http://acme.aegoryx.test/panel/files', [
                'file' => UploadedFile::fake()->createWithContent('tiny.txt', 'x'),
            ])
            ->assertSessionHasErrors('limit');

        $this->assertSame(0, TenantFile::query()->count());
    }

    private function tenant(): Tenant
    {
        return Tenant::query()->create([
            'name' => 'Acme Tenant',
            'slug' => 'acme',
            'schema_name' => 'tenant_acme',
            'status' => TenantStatus::Active,
            'deployment_type' => TenantDeploymentType::Saas,
            'billing_model' => TenantBillingModel::Subscription,
            'license_type' => TenantLicenseType::SaasSubscription,
        ]);
    }

    private function domain(Tenant $tenant): TenantDomain
    {
        return TenantDomain::query()->create([
            'tenant_id' => $tenant->id,
            'domain' => 'acme.aegoryx.test',
            'type' => TenantDomainType::Primary,
            'status' => TenantDomainStatus::Verified,
        ]);
    }

    private function manualOverride(Tenant $tenant, string $featureKey, bool $enabled, array $config = []): TenantFeature
    {
        return TenantFeature::query()->create([
            'tenant_id' => $tenant->id,
            'feature' => $featureKey,
            'enabled' => $enabled,
            'source' => TenantFeatureSource::Manual,
            'reason' => 'Test entitlement.',
            'config' => $config,
        ]);
    }

    private function user(): User
    {
        return User::query()->create([
            'name' => 'Member',
            'email' => 'member@example.test',
            'password' => 'secret-password',
        ]);
    }
}
