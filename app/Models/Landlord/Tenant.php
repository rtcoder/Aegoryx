<?php

namespace App\Models\Landlord;

use App\Modules\Tenancy\Enums\TenantBillingModel;
use App\Modules\Tenancy\Enums\TenantDeploymentType;
use App\Modules\Tenancy\Enums\TenantLicenseType;
use App\Modules\Tenancy\Enums\TenantStatus;
use App\Support\Localization\Locale;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $schema_name
 * @property TenantStatus $status
 * @property Locale $locale
 * @property TenantDeploymentType $deployment_type
 * @property TenantBillingModel $billing_model
 * @property TenantLicenseType $license_type
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
#[Fillable([
    'name',
    'slug',
    'schema_name',
    'status',
    'locale',
    'deployment_type',
    'billing_model',
    'license_type',
    'created_by',
    'updated_by',
    'deleted_by',
])]
final class Tenant extends Model
{
    use SoftDeletes;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'billing_model' => TenantBillingModel::class,
            'deployment_type' => TenantDeploymentType::class,
            'license_type' => TenantLicenseType::class,
            'locale' => Locale::class,
            'status' => TenantStatus::class,
        ];
    }
}
