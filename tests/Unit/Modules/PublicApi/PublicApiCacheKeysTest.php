<?php

namespace Tests\Unit\Modules\PublicApi;

use App\Models\Landlord\Tenant;
use App\Models\Tenant\PublishedPage;
use App\Modules\PublicApi\Support\PublicApiCacheKeys;
use Tests\TestCase;

final class PublicApiCacheKeysTest extends TestCase
{
    public function test_published_page_cache_key_is_scoped_by_tenant(): void
    {
        $page = new PublishedPage([
            'slug' => 'home',
            'updated_at' => now(),
        ]);
        $firstTenant = new Tenant;
        $firstTenant->setAttribute('id', 1);
        $secondTenant = new Tenant;
        $secondTenant->setAttribute('id', 2);
        $keys = new PublicApiCacheKeys;

        $this->assertNotSame(
            $keys->publishedPage($firstTenant, $page),
            $keys->publishedPage($secondTenant, $page),
        );
        $this->assertStringStartsWith('public-api:tenant:1:', $keys->publishedPage($firstTenant, $page));
    }
}
