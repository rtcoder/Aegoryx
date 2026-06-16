<?php

namespace Tests\Unit\Models\Landlord;

use App\Models\Landlord\License;
use App\Models\Landlord\Plan;
use App\Models\Landlord\Subscription;
use App\Models\Landlord\SystemInstallation;
use App\Models\Landlord\Tenant;
use App\Models\Landlord\TenantDomain;
use App\Models\Landlord\TenantFeature;
use App\Modules\Billing\Enums\PlanStatus;
use App\Modules\Billing\Enums\SubscriptionStatus;
use App\Modules\Entitlements\Enums\SystemFeature;
use App\Modules\Entitlements\Enums\TenantFeatureSource;
use App\Modules\Licensing\Enums\LicenseStatus;
use App\Modules\Tenancy\Enums\SystemInstallationStatus;
use App\Modules\Tenancy\Enums\TenantBillingModel;
use App\Modules\Tenancy\Enums\TenantDeploymentType;
use App\Modules\Tenancy\Enums\TenantDomainStatus;
use App\Modules\Tenancy\Enums\TenantDomainType;
use App\Modules\Tenancy\Enums\TenantLicenseType;
use App\Modules\Tenancy\Enums\TenantStatus;
use PHPUnit\Framework\TestCase;

final class LandlordEnumCastsTest extends TestCase
{
    public function test_license_status_is_cast_to_enum(): void
    {
        $model = new License(['status' => LicenseStatus::Inactive->value]);

        $this->assertSame(LicenseStatus::Inactive, $model->status);
    }

    public function test_plan_status_is_cast_to_enum(): void
    {
        $model = new Plan(['status' => PlanStatus::Active->value]);

        $this->assertSame(PlanStatus::Active, $model->status);
    }

    public function test_subscription_status_is_cast_to_enum(): void
    {
        $model = new Subscription(['status' => SubscriptionStatus::Inactive->value]);

        $this->assertSame(SubscriptionStatus::Inactive, $model->status);
    }

    public function test_system_installation_status_and_deployment_type_are_cast_to_enums(): void
    {
        $model = new SystemInstallation([
            'deployment_type' => TenantDeploymentType::Saas->value,
            'status' => SystemInstallationStatus::Active->value,
        ]);

        $this->assertSame(TenantDeploymentType::Saas, $model->deployment_type);
        $this->assertSame(SystemInstallationStatus::Active, $model->status);
    }

    public function test_tenant_lifecycle_fields_are_cast_to_enums(): void
    {
        $model = new Tenant([
            'billing_model' => TenantBillingModel::Subscription->value,
            'deployment_type' => TenantDeploymentType::Saas->value,
            'license_type' => TenantLicenseType::SaasSubscription->value,
            'status' => TenantStatus::Active->value,
        ]);

        $this->assertSame(TenantBillingModel::Subscription, $model->billing_model);
        $this->assertSame(TenantDeploymentType::Saas, $model->deployment_type);
        $this->assertSame(TenantLicenseType::SaasSubscription, $model->license_type);
        $this->assertSame(TenantStatus::Active, $model->status);
    }

    public function test_tenant_domain_type_and_status_are_cast_to_enums(): void
    {
        $model = new TenantDomain([
            'type' => TenantDomainType::Primary->value,
            'status' => TenantDomainStatus::Pending->value,
        ]);

        $this->assertSame(TenantDomainType::Primary, $model->type);
        $this->assertSame(TenantDomainStatus::Pending, $model->status);
    }

    public function test_tenant_feature_source_is_cast_to_enum(): void
    {
        $model = new TenantFeature([
            'feature' => SystemFeature::Crm->value,
            'source' => TenantFeatureSource::Manual->value,
        ]);

        $this->assertSame(SystemFeature::Crm, $model->feature);
        $this->assertSame(TenantFeatureSource::Manual, $model->source);
    }
}
