<?php

namespace App\Modules\PublicApi\Support;

use App\Models\Landlord\Tenant;
use App\Models\Tenant\PublishedPage;

final readonly class PublicApiCacheKeys
{
    public function publishedPage(Tenant $tenant, PublishedPage $page): string
    {
        return $this->publishedPageSlug($tenant, $page->slug);
    }

    public function publishedPageSlug(Tenant $tenant, string $slug): string
    {
        return sprintf(
            'public-api:tenant:%s:published-page:%s',
            $tenant->id,
            $slug,
        );
    }
}
