<?php

namespace App\Modules\Tenancy\Actions;

use App\Models\Landlord\Tenant;
use App\Modules\Tenancy\Enums\TenantBillingModel;
use App\Modules\Tenancy\Enums\TenantDeploymentType;
use App\Modules\Tenancy\Enums\TenantLicenseType;
use App\Modules\Tenancy\Enums\TenantStatus;
use App\Services\Tenancy\PostgresSchemaManager;
use App\Support\Localization\Locale;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

final readonly class CreateTenantAction
{
    public function __construct(
        private PostgresSchemaManager $schemas,
    ) {}

    public function handle(string $name, ?string $slug = null, Locale $locale = Locale::Polish, bool $migrate = true): Tenant
    {
        $tenant = DB::transaction(function () use ($name, $slug, $locale): Tenant {
            $tenant = Tenant::query()->create([
                'name' => $name,
                'slug' => $this->uniqueSlug($slug ?: $name),
                'schema_name' => 'tenant_pending_'.Str::lower(Str::random(16)),
                'status' => TenantStatus::Active,
                'locale' => $locale,
                'deployment_type' => TenantDeploymentType::Saas,
                'billing_model' => TenantBillingModel::Subscription,
                'license_type' => TenantLicenseType::SaasSubscription,
            ]);

            $tenant->forceFill([
                'schema_name' => $this->schemaName($tenant),
            ])->save();

            return $tenant->refresh();
        });

        $this->schemas->create($tenant->schema_name);

        if ($migrate) {
            $this->migrateTenant($tenant);
        }

        return $tenant;
    }

    private function migrateTenant(Tenant $tenant): void
    {
        $this->schemas->setSearchPath($tenant->schema_name);

        try {
            $exitCode = Artisan::call('migrate', [
                '--path' => 'database/migrations/tenant',
                '--force' => true,
            ]);

            if ($exitCode !== 0) {
                throw new RuntimeException("Tenant migrations failed for tenant [{$tenant->id}] with exit code [{$exitCode}].");
            }
        } finally {
            $this->schemas->resetSearchPath();
        }
    }

    private function schemaName(Tenant $tenant): string
    {
        return "tenant_{$tenant->id}";
    }

    private function uniqueSlug(string $source): string
    {
        $base = Str::slug($source);

        if ($base === '') {
            $base = 'tenant';
        }

        $slug = $base;
        $suffix = 2;

        while (Tenant::query()->where('slug', $slug)->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
