<?php

namespace App\Models\Landlord;

use App\Modules\Billing\Enums\BillingEventStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $provider
 * @property string $provider_event_id
 * @property string $event_type
 * @property int|null $tenant_id
 * @property int|null $subscription_id
 * @property BillingEventStatus $status
 * @property array<string, mixed>|null $payload
 * @property Carbon|null $processed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Subscription|null $subscription
 * @property-read Tenant|null $tenant
 */
#[Fillable([
    'provider',
    'provider_event_id',
    'event_type',
    'tenant_id',
    'subscription_id',
    'status',
    'payload',
    'processed_at',
])]
final class BillingEvent extends Model
{
    /**
     * @return BelongsTo<Subscription, $this>
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
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
            'payload' => 'array',
            'processed_at' => 'datetime',
            'status' => BillingEventStatus::class,
        ];
    }
}
