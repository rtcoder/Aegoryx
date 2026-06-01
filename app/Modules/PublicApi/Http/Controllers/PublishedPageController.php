<?php

namespace App\Modules\PublicApi\Http\Controllers;

use App\Models\Tenant\PublishedPage;
use App\Modules\PublicApi\Http\Resources\PublishedPageResource;

final readonly class PublishedPageController
{
    public function show(string $slug): PublishedPageResource
    {
        $page = PublishedPage::query()
            ->where('slug', $slug)
            ->firstOrFail();

        return PublishedPageResource::make($page);
    }
}
