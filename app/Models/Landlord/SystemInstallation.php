<?php

namespace App\Models\Landlord;

use App\Modules\Tenancy\Enums\SystemInstallationStatus;
use App\Modules\Tenancy\Enums\TenantDeploymentType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $installation_uuid
 * @property TenantDeploymentType $deployment_type
 * @property SystemInstallationStatus $status
 * @property string|null $version
 * @property Carbon|null $installed_at
 * @property Carbon|null $last_seen_at
 * @property array<string, mixed>|null $metadata
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'installation_uuid',
    'deployment_type',
    'status',
    'version',
    'installed_at',
    'last_seen_at',
    'metadata',
])]
final class SystemInstallation extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'deployment_type' => TenantDeploymentType::class,
            'installed_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'metadata' => 'array',
            'status' => SystemInstallationStatus::class,
        ];
    }
}
