<?php

namespace App\Models\Landlord;

use App\Modules\Licensing\Enums\LicenseStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

final class License extends Model
{
    use SoftDeletes;

    protected $fillable = [
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
    ];

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
