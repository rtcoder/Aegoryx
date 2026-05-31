<?php

namespace App\Models\Landlord;

use App\Modules\Billing\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Model;

final class Subscription extends Model
{
    protected $fillable = [
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
    ];

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
