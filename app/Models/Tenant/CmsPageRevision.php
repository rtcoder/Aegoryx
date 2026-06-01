<?php

namespace App\Models\Tenant;

use App\Modules\Cms\Enums\CmsPageStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $cms_page_id
 * @property int $version
 * @property string $title
 * @property string $slug
 * @property CmsPageStatus $status
 * @property array<string, mixed> $content
 * @property int|null $created_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read CmsPage $page
 */
#[Fillable([
    'cms_page_id',
    'version',
    'title',
    'slug',
    'status',
    'content',
    'created_by',
])]
final class CmsPageRevision extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'content' => 'array',
            'status' => CmsPageStatus::class,
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
