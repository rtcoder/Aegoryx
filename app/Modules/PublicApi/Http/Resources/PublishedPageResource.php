<?php

namespace App\Modules\PublicApi\Http\Resources;

use App\Models\Tenant\PublishedPage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin PublishedPage
 */
final class PublishedPageResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->slug,
            'title' => $this->title,
            'content' => $this->content,
            'published_at' => $this->published_at->toISOString(),
        ];
    }
}
