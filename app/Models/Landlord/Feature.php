<?php

namespace App\Models\Landlord;

use App\Modules\Entitlements\Enums\FeatureStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $key
 * @property string $name
 * @property string|null $description
 * @property FeatureStatus $status
 * @property array<string, mixed>|null $default_config
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, TenantFeature> $tenantFeatures
 */
#[Fillable([
    'key',
    'name',
    'description',
    'status',
    'default_config',
    'created_by',
    'updated_by',
    'deleted_by',
])]
final class Feature extends Model
{
    use SoftDeletes;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'default_config' => 'array',
            'status' => FeatureStatus::class,
        ];
    }

    /**
     * @return HasMany<TenantFeature, $this>
     */
    public function tenantFeatures(): HasMany
    {
        return $this->hasMany(TenantFeature::class);
    }
}
