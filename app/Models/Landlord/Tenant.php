<?php

namespace App\Models\Landlord;

use App\Modules\Tenancy\Enums\TenantBillingModel;
use App\Modules\Tenancy\Enums\TenantDeploymentType;
use App\Modules\Tenancy\Enums\TenantLicenseType;
use App\Modules\Tenancy\Enums\TenantStatus;
use App\Support\Localization\Locale;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
 * @property array<int, string>|null $public_api_cors_allowed_origins
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, TenantFeature> $features
 * @property-read Collection<int, License> $licenses
 * @property-read Collection<int, Subscription> $subscriptions
 * @property-read Collection<int, TenantDomain> $domains
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
    'public_api_cors_allowed_origins',
    'created_by',
    'updated_by',
    'deleted_by',
])]
final class Tenant extends Model
{
    use SoftDeletes;

    /**
     * @return HasMany<TenantFeature, $this>
     */
    public function features(): HasMany
    {
        return $this->hasMany(TenantFeature::class);
    }

    /**
     * @return HasMany<License, $this>
     */
    public function licenses(): HasMany
    {
        return $this->hasMany(License::class);
    }

    /**
     * @return HasMany<Subscription, $this>
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * @return HasMany<TenantDomain, $this>
     */
    public function domains(): HasMany
    {
        return $this->hasMany(TenantDomain::class);
    }

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
            'public_api_cors_allowed_origins' => 'array',
            'status' => TenantStatus::class,
        ];
    }
}
