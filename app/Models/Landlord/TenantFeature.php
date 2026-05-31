<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;

final class TenantFeature extends Model
{
    protected $fillable = [
        'tenant_id',
        'feature_id',
        'enabled',
        'source',
        'reason',
        'config',
        'created_by',
        'updated_by',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'config' => 'array',
        ];
    }
}
