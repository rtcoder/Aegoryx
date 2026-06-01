<?php

namespace App\Models\Landlord;

use App\Modules\Entitlements\Enums\TenantFeatureSource;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $tenant_id
 * @property int $feature_id
 * @property bool $enabled
 * @property TenantFeatureSource $source
 * @property string|null $reason
 * @property array<string, mixed>|null $config
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Feature $feature
 * @property-read Tenant $tenant
 */
#[Fillable([
    'tenant_id',
    'feature_id',
    'enabled',
    'source',
    'reason',
    'config',
    'created_by',
    'updated_by',
])]
final class TenantFeature extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'config' => 'array',
            'source' => TenantFeatureSource::class,
        ];
    }

    /**
     * @return BelongsTo<Feature, $this>
     */
    public function feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class);
    }

    /**
     * @return BelongsTo<Tenant, $this>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
