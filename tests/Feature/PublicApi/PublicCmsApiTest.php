<?php

namespace Tests\Feature\PublicApi;

use App\Models\Landlord\Tenant;
use App\Models\Landlord\TenantDomain;
use App\Models\Tenant\CmsPage;
use App\Models\Tenant\PublishedPage;
use App\Modules\Cms\Enums\CmsPageStatus;
use App\Modules\Tenancy\Enums\TenantBillingModel;
use App\Modules\Tenancy\Enums\TenantDeploymentType;
use App\Modules\Tenancy\Enums\TenantDomainStatus;
use App\Modules\Tenancy\Enums\TenantDomainType;
use App\Modules\Tenancy\Enums\TenantLicenseType;
use App\Modules\Tenancy\Enums\TenantStatus;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class PublicCmsApiTest extends TestCase
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

        config()->set('cache.default', 'array');
        config()->set('cors.allowed_origins', ['https://example.test']);
        config()->set('aegoryx.public_api.cache.ttl_seconds', 300);
        config()->set('aegoryx.public_api.cors.allowed_origins', ['https://example.test']);
        config()->set('aegoryx.public_api.rate_limit.decay_seconds', 60);
        config()->set('aegoryx.public_api.rate_limit.max_attempts', 2);

        Cache::flush();
    }

    public function test_public_api_returns_published_page_snapshot(): void
    {
        $tenant = $this->tenant();
        $this->domain($tenant);
        $page = $this->page(['slug' => 'home']);

        PublishedPage::query()->create([
            'cms_page_id' => $page->id,
            'title' => 'Homepage',
            'slug' => 'home',
            'content' => ['blocks' => [['type' => 'text', 'body' => 'Hello']]],
            'published_at' => now(),
        ]);

        $this
            ->getJson('http://acme.aegoryx.test/api/public/cms/pages/home')
            ->assertOk()
            ->assertJsonPath('data.slug', 'home')
            ->assertJsonPath('data.title', 'Homepage')
            ->assertJsonMissingPath('data.cms_page_id')
            ->assertJsonMissingPath('data.created_by')
            ->assertJsonMissingPath('data.updated_by');
    }

    public function test_public_api_does_not_return_draft_page(): void
    {
        $tenant = $this->tenant([
            'public_api_cors_allowed_origins' => ['https://example.test'],
        ]);
        $this->domain($tenant);
        $this->page(['slug' => 'draft-only']);

        $this
            ->getJson('http://acme.aegoryx.test/api/public/cms/pages/draft-only')
            ->assertNotFound();
    }

    public function test_public_api_rejects_unknown_or_suspended_tenant(): void
    {
        $tenant = $this->tenant(['status' => TenantStatus::Suspended]);
        $this->domain($tenant);

        $this
            ->getJson('http://acme.aegoryx.test/api/public/cms/pages/home')
            ->assertNotFound();

        $this
            ->getJson('http://unknown.aegoryx.test/api/public/cms/pages/home')
            ->assertNotFound();
    }

    public function test_public_api_write_methods_are_not_available(): void
    {
        $tenant = $this->tenant();
        $this->domain($tenant);

        $this
            ->postJson('http://acme.aegoryx.test/api/public/cms/pages/home')
            ->assertStatus(405);
    }

    public function test_public_api_rate_limits_requests_per_host_and_ip(): void
    {
        $tenant = $this->tenant();
        $this->domain($tenant);
        $page = $this->page(['slug' => 'home']);

        PublishedPage::query()->create([
            'cms_page_id' => $page->id,
            'title' => 'Homepage',
            'slug' => 'home',
            'content' => ['blocks' => [['type' => 'text', 'body' => 'Hello']]],
            'published_at' => now(),
        ]);

        $this->getJson('http://acme.aegoryx.test/api/public/cms/pages/home')
            ->assertOk()
            ->assertHeader('X-RateLimit-Limit', '2')
            ->assertHeader('X-RateLimit-Remaining', '1');

        $this->getJson('http://acme.aegoryx.test/api/public/cms/pages/home')
            ->assertOk()
            ->assertHeader('X-RateLimit-Remaining', '0');

        $this->getJson('http://acme.aegoryx.test/api/public/cms/pages/home')
            ->assertTooManyRequests()
            ->assertHeader('X-RateLimit-Limit', '2');
    }

    public function test_public_api_caches_published_page_payload(): void
    {
        $tenant = $this->tenant();
        $this->domain($tenant);
        $page = $this->page(['slug' => 'home']);
        $published = PublishedPage::query()->create([
            'cms_page_id' => $page->id,
            'title' => 'Homepage',
            'slug' => 'home',
            'content' => ['blocks' => [['type' => 'text', 'body' => 'Hello']]],
            'published_at' => now(),
        ]);

        $this
            ->getJson('http://acme.aegoryx.test/api/public/cms/pages/home')
            ->assertOk()
            ->assertJsonPath('data.title', 'Homepage');

        DB::table('published_pages')
            ->where('id', $published->id)
            ->update(['title' => 'Changed without timestamp bump']);

        $this
            ->getJson('http://acme.aegoryx.test/api/public/cms/pages/home')
            ->assertOk()
            ->assertJsonPath('data.title', 'Homepage');
    }

    public function test_public_api_cors_allow_list(): void
    {
        $tenant = $this->tenant();
        $this->domain($tenant);
        $page = $this->page(['slug' => 'home']);

        PublishedPage::query()->create([
            'cms_page_id' => $page->id,
            'title' => 'Homepage',
            'slug' => 'home',
            'content' => ['blocks' => [['type' => 'text', 'body' => 'Hello']]],
            'published_at' => now(),
        ]);

        $this
            ->withHeader('Origin', 'https://example.test')
            ->getJson('http://acme.aegoryx.test/api/public/cms/pages/home')
            ->assertOk()
            ->assertHeader('Access-Control-Allow-Origin', 'https://example.test')
            ->assertHeader('Access-Control-Allow-Methods', 'GET, OPTIONS');

        $this
            ->withHeader('Origin', 'https://blocked.test')
            ->getJson('http://acme.aegoryx.test/api/public/cms/pages/home')
            ->assertForbidden();
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function tenant(array $attributes = []): Tenant
    {
        return Tenant::query()->create(array_merge([
            'name' => 'Acme Tenant',
            'slug' => 'acme',
            'schema_name' => 'tenant_acme',
            'status' => TenantStatus::Active,
            'deployment_type' => TenantDeploymentType::Saas,
            'billing_model' => TenantBillingModel::Subscription,
            'license_type' => TenantLicenseType::SaasSubscription,
        ], $attributes));
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

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function page(array $attributes = []): CmsPage
    {
        return CmsPage::query()->create(array_merge([
            'title' => 'Draft',
            'slug' => 'draft',
            'status' => CmsPageStatus::Draft,
            'draft_content' => ['body' => 'Private draft'],
        ], $attributes));
    }
}
