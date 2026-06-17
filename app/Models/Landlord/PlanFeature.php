<?php

namespace App\Models\Landlord;

use App\Modules\Entitlements\Enums\SystemFeature;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $plan_id
 * @property SystemFeature $feature
 * @property bool $enabled
 * @property array<string, mixed>|null $config
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Plan $plan
 */
#[Fillable([
    'plan_id',
    'feature',
    'enabled',
    'config',
])]
final class PlanFeature extends Model
{
    /**
     * @return BelongsTo<Plan, $this>
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'config' => 'array',
            'feature' => SystemFeature::class,
        ];
    }
}
