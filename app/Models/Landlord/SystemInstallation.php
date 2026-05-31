<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;

final class SystemInstallation extends Model
{
    protected $fillable = [
        'installation_uuid',
        'deployment_type',
        'status',
        'version',
        'installed_at',
        'last_seen_at',
        'metadata',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'installed_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'metadata' => 'array',
        ];
    }
}
