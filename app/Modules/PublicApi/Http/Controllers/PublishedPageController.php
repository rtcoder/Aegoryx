<?php

namespace App\Modules\PublicApi\Http\Controllers;

use App\Models\Tenant\PublishedPage;
use App\Modules\PublicApi\Http\Resources\PublishedPageResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

final readonly class PublishedPageController
{
    public function show(Request $request, string $slug): JsonResponse
    {
        $tenant = $request->attributes->get('tenant');
        $page = PublishedPage::query()
            ->where('slug', $slug)
            ->firstOrFail();
        $ttl = (int) config('aegoryx.public_api.cache.ttl_seconds', 300);
        $cacheKey = sprintf(
            'public-api:tenant:%s:published-page:%s:%s',
            $tenant?->id ?? 'unknown',
            $page->slug,
            $page->updated_at?->timestamp ?? 0,
        );

        $payload = Cache::remember($cacheKey, $ttl, fn (): array => PublishedPageResource::make($page)->resolve($request));

        return response()->json([
            'data' => $payload,
        ]);
    }
}
