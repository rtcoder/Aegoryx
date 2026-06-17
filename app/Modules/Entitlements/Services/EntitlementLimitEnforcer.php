<?php

namespace App\Modules\Entitlements\Services;

use App\Models\Tenant\CmsPage;
use App\Models\Tenant\CrmContact;
use App\Models\Tenant\TenantFile;
use App\Services\Tenancy\TenancyManager;
use Illuminate\Validation\ValidationException;

final readonly class EntitlementLimitEnforcer
{
    public function __construct(
        private EffectiveEntitlements $entitlements,
        private TenancyManager $tenancy,
    ) {}

    public function assertCanCreateCmsPage(): void
    {
        $limit = $this->limit('cms.pages');

        if ($limit === null) {
            return;
        }

        if (CmsPage::query()->count() >= $limit) {
            $this->fail(__('errors.limit_cms_pages_reached', ['limit' => $limit]));
        }
    }

    public function assertCanCreateCrmContact(): void
    {
        $limit = $this->limit('crm.contacts');

        if ($limit === null) {
            return;
        }

        if (CrmContact::query()->count() >= $limit) {
            $this->fail(__('errors.limit_crm_contacts_reached', ['limit' => $limit]));
        }
    }

    public function assertCanStoreFileBytes(int $newFileSizeBytes): void
    {
        $limitMb = $this->limit('files.storage_mb');

        if ($limitMb === null) {
            return;
        }

        $limitBytes = (int) floor($limitMb * 1024 * 1024);
        $currentBytes = (int) TenantFile::query()->sum('size_bytes');

        if ($currentBytes + $newFileSizeBytes > $limitBytes) {
            $this->fail(__('errors.limit_file_storage_reached', ['limit' => $limitMb]));
        }
    }

    private function limit(string $key): int|float|null
    {
        $tenant = $this->tenancy->current();

        if (! $tenant) {
            return null;
        }

        $limit = $this->entitlements->limit($tenant, $key);

        if ($limit === null || $limit === 'unlimited') {
            return null;
        }

        if (is_int($limit) || is_float($limit)) {
            return $limit;
        }

        if (is_numeric($limit)) {
            return $limit + 0;
        }

        return null;
    }

    private function fail(string $message): never
    {
        throw ValidationException::withMessages([
            'limit' => $message,
        ]);
    }
}
