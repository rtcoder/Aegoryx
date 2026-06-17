<?php

namespace App\Modules\PublicApi\Support;

use App\Models\Landlord\Tenant;
use App\Models\Tenant\PublishedPage;

final readonly class PublicApiCacheKeys
{
    public function publishedPage(Tenant $tenant, PublishedPage $page): string
    {
        return sprintf(
            'public-api:tenant:%s:published-page:%s:%s',
            $tenant->id,
            $page->slug,
            $page->updated_at?->timestamp ?? 0,
        );
    }
}
