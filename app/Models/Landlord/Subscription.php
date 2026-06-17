<?php

namespace App\Models\Landlord;

use App\Modules\Billing\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $tenant_id
 * @property int|null $plan_id
 * @property string $provider
 * @property string|null $provider_subscription_id
 * @property SubscriptionStatus $status
 * @property Carbon|null $trial_ends_at
 * @property Carbon|null $current_period_ends_at
 * @property Carbon|null $cancelled_at
 * @property array<string, mixed>|null $provider_payload
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Plan|null $plan
 * @property-read Tenant $tenant
 */
#[Fillable([
    'tenant_id',
    'plan_id',
    'provider',
    'provider_subscription_id',
    'status',
    'trial_ends_at',
    'current_period_ends_at',
    'cancelled_at',
    'provider_payload',
    'created_by',
    'updated_by',
])]
final class Subscription extends Model
{
    /**
     * @return BelongsTo<Plan, $this>
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * @return BelongsTo<Tenant, $this>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
            'current_period_ends_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'provider_payload' => 'array',
            'status' => SubscriptionStatus::class,
        ];
    }
}
