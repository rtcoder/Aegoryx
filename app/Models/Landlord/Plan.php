<?php

namespace App\Models\Landlord;

use App\Modules\Billing\Enums\PlanStatus;
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
 * @property PlanStatus $status
 * @property string|null $billing_interval
 * @property int $sort_order
 * @property array<string, mixed>|null $limits
 * @property array<string, mixed>|null $metadata
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, PlanFeature> $features
 */
#[Fillable([
    'key',
    'name',
    'status',
    'billing_interval',
    'sort_order',
    'limits',
    'metadata',
    'created_by',
    'updated_by',
    'deleted_by',
])]
final class Plan extends Model
{
    use SoftDeletes;

    /**
     * @return HasMany<PlanFeature, $this>
     */
    public function features(): HasMany
    {
        return $this->hasMany(PlanFeature::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'limits' => 'array',
            'metadata' => 'array',
            'status' => PlanStatus::class,
        ];
    }
}
