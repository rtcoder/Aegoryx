<?php

namespace Tests\Feature\TenantPanel;

use App\Livewire\Tenant\Cms\Pages\Index;
use App\Models\Landlord\Tenant;
use App\Models\Landlord\TenantDomain;
use App\Models\Landlord\TenantFeature;
use App\Models\Tenant\CmsPage;
use App\Models\Tenant\PublishedPage;
use App\Models\Tenant\User;
use App\Modules\Cms\Enums\CmsPageStatus;
use App\Modules\Entitlements\Enums\TenantFeatureSource;
use App\Modules\Identity\Enums\TenantUserRole;
use App\Modules\Tenancy\Enums\TenantBillingModel;
use App\Modules\Tenancy\Enums\TenantDeploymentType;
use App\Modules\Tenancy\Enums\TenantDomainStatus;
use App\Modules\Tenancy\Enums\TenantDomainType;
use App\Modules\Tenancy\Enums\TenantLicenseType;
use App\Modules\Tenancy\Enums\TenantStatus;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;
use Tests\TestCase;

final class TenantCmsPagesTest extends TestCase
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
    }

    public function test_cms_page_panel_can_create_publish_and_unpublish_page(): void
    {
        $tenant = $this->tenant();
        $this->domain($tenant);
        $this->manualOverride($tenant, 'cms', true);
        $this->actingAs($this->user(), 'web');

        $this
            ->get('http://acme.aegoryx.test/panel/cms')
            ->assertOk()
            ->assertSee(__('cms.title'))
            ->assertSee(__('cms.new_page'));

        Livewire::test(Index::class)
            ->set('title', 'Homepage')
            ->set('slug', 'home')
            ->set('body', 'Public body')
            ->call('save')
            ->assertHasNoErrors();

        $page = CmsPage::query()->firstOrFail();

        $this->assertSame('Homepage', $page->title);
        $this->assertSame(CmsPageStatus::Draft, $page->status);

        Livewire::test(Index::class)
            ->call('publish', $page->id)
            ->assertHasNoErrors();

        $this->assertSame(CmsPageStatus::Published, $page->refresh()->status);
        $this->assertSame(1, PublishedPage::query()->where('cms_page_id', $page->id)->count());

        Livewire::test(Index::class)
            ->call('unpublish', $page->id)
            ->assertHasNoErrors();

        $this->assertSame(CmsPageStatus::Draft, $page->refresh()->status);
        $this->assertSame(0, PublishedPage::query()->where('cms_page_id', $page->id)->count());
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

    private function manualOverride(Tenant $tenant, string $featureKey, bool $enabled): TenantFeature
    {
        return TenantFeature::query()->create([
            'tenant_id' => $tenant->id,
            'feature' => $featureKey,
            'enabled' => $enabled,
            'source' => TenantFeatureSource::Manual,
            'reason' => 'Test entitlement.',
        ]);
    }

    private function user(): User
    {
        return User::query()->create([
            'name' => 'Member',
            'email' => 'member@example.test',
            'password' => 'secret-password',
            'role' => TenantUserRole::Member,
        ]);
    }
}
