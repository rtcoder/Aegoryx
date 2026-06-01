<?php

namespace App\Models\Landlord;

use App\Modules\AdminConsole\Enums\SupportSessionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class SupportSession extends Model
{
    protected $fillable = [
        'tenant_id',
        'actor_id',
        'status',
        'reason',
        'started_at',
        'expires_at',
        'ended_at',
        'ip',
        'user_agent',
    ];

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
