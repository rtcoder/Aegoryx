<?php

namespace App\Models\Landlord;

use App\Modules\AdminConsole\Enums\SupportSessionStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $tenant_id
 * @property int $actor_id
 * @property SupportSessionStatus $status
 * @property string $reason
 * @property Carbon $started_at
 * @property Carbon $expires_at
 * @property Carbon|null $ended_at
 * @property string|null $ip
 * @property string|null $user_agent
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Identity $actor
 * @property-read Tenant $tenant
 */
#[Fillable([
    'tenant_id',
    'actor_id',
    'status',
    'reason',
    'started_at',
    'expires_at',
    'ended_at',
    'ip',
    'user_agent',
])]
final class SupportSession extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ended_at' => 'datetime',
            'expires_at' => 'datetime',
            'started_at' => 'datetime',
            'status' => SupportSessionStatus::class,
        ];
    }

    /**
     * @return BelongsTo<Identity, $this>
     */
    public function actor(): BelongsTo
    {
        return $this->belongsTo(Identity::class, 'actor_id');
    }

    /**
     * @return BelongsTo<Tenant, $this>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function isActive(): bool
    {
        return $this->status === SupportSessionStatus::Active
            && $this->expires_at->isFuture();
    }
}
