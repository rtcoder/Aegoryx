<?php

namespace App\Models\Landlord;

use App\Modules\Licensing\Enums\LicenseStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $tenant_id
 * @property string $license_key_hash
 * @property string $type
 * @property LicenseStatus $status
 * @property Carbon|null $issued_at
 * @property Carbon|null $expires_at
 * @property Carbon|null $last_verified_at
 * @property array<string, mixed>|null $payload
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Tenant|null $tenant
 */
#[Fillable([
    'tenant_id',
    'license_key_hash',
    'type',
    'status',
    'issued_at',
    'expires_at',
    'last_verified_at',
    'payload',
    'created_by',
    'updated_by',
    'deleted_by',
])]
final class License extends Model
{
    use SoftDeletes;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'expires_at' => 'datetime',
            'last_verified_at' => 'datetime',
            'payload' => 'array',
            'status' => LicenseStatus::class,
        ];
    }

    /**
     * @return BelongsTo<Tenant, $this>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
