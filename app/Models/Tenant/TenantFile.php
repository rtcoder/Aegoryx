<?php

namespace App\Models\Tenant;

use App\Modules\Files\Enums\FileVisibility;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $disk
 * @property string $path
 * @property string $original_name
 * @property string|null $mime_type
 * @property int $size_bytes
 * @property string|null $checksum_sha256
 * @property FileVisibility $visibility
 * @property Carbon|null $expires_at
 * @property int|null $owner_id
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User|null $owner
 */
#[Fillable([
    'disk',
    'path',
    'original_name',
    'mime_type',
    'size_bytes',
    'checksum_sha256',
    'visibility',
    'expires_at',
    'owner_id',
    'created_by',
    'updated_by',
    'deleted_by',
])]
final class TenantFile extends Model
{
    use SoftDeletes;

    protected $table = 'files';

    /**
     * @return BelongsTo<User, $this>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'visibility' => FileVisibility::class,
        ];
    }
}
