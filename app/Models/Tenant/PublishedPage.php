<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $cms_page_id
 * @property string $title
 * @property string $slug
 * @property array<string, mixed> $content
 * @property Carbon $published_at
 * @property int|null $published_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read CmsPage $page
 */
#[Fillable([
    'cms_page_id',
    'title',
    'slug',
    'content',
    'published_at',
    'published_by',
])]
final class PublishedPage extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'content' => 'array',
            'published_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<CmsPage, $this>
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(CmsPage::class, 'cms_page_id');
    }
}
