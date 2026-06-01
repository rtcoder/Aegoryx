<?php

namespace App\Models\Tenant;

use App\Modules\Cms\Enums\CmsPageStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property CmsPageStatus $status
 * @property array<string, mixed> $draft_content
 * @property Carbon|null $published_at
 * @property int|null $published_by
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, CmsPageRevision> $revisions
 * @property-read PublishedPage|null $publishedSnapshot
 */
#[Fillable([
    'title',
    'slug',
    'status',
    'draft_content',
    'published_at',
    'published_by',
    'created_by',
    'updated_by',
    'deleted_by',
])]
final class CmsPage extends Model
{
    use SoftDeletes;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'draft_content' => 'array',
            'published_at' => 'datetime',
            'status' => CmsPageStatus::class,
        ];
    }

    /**
     * @return HasMany<CmsPageRevision, $this>
     */
    public function revisions(): HasMany
    {
        return $this->hasMany(CmsPageRevision::class);
    }

    /**
     * @return HasOne<PublishedPage, $this>
     */
    public function publishedSnapshot(): HasOne
    {
        return $this->hasOne(PublishedPage::class);
    }
}
