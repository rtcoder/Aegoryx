<?php

namespace App\Models\Landlord;

use App\Modules\Tenancy\Enums\TenantBillingModel;
use App\Modules\Tenancy\Enums\TenantDeploymentType;
use App\Modules\Tenancy\Enums\TenantLicenseType;
use App\Modules\Tenancy\Enums\TenantStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Tenant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'schema_name',
        'status',
        'deployment_type',
        'billing_model',
        'license_type',
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
            'billing_model' => TenantBillingModel::class,
            'deployment_type' => TenantDeploymentType::class,
            'license_type' => TenantLicenseType::class,
            'status' => TenantStatus::class,
        ];
    }
}
