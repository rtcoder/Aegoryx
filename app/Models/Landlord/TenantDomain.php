<?php

namespace App\Models\Landlord;

use App\Modules\Tenancy\Enums\TenantDomainStatus;
use App\Modules\Tenancy\Enums\TenantDomainType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class TenantDomain extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'domain',
        'type',
        'status',
        'verified_at',
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
            'status' => TenantDomainStatus::class,
            'type' => TenantDomainType::class,
            'verified_at' => 'datetime',
        ];
    }
}
