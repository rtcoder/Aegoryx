<?php

namespace App\Support\Queue;

use App\Models\Landlord\Tenant;
use App\Services\Tenancy\TenancyManager;
use Closure;
use LogicException;

trait InteractsWithTenantContext
{
    public ?int $tenantId = null;

    public function forTenant(Tenant $tenant): static
    {
        $this->tenantId = $tenant->id;

        return $this;
    }

    /**
     * @template TReturn
     *
     * @param  Closure(Tenant): TReturn  $callback
     * @return TReturn
     */
    protected function runWithTenantContext(TenancyManager $tenancy, Closure $callback): mixed
    {
        if ($this->tenantId === null) {
            throw new LogicException('Tenant-aware jobs must be dispatched with tenantId.');
        }

        $tenant = Tenant::query()->findOrFail($this->tenantId);

        $tenancy->initialize($tenant);

        try {
            return $callback($tenant);
        } finally {
            $tenancy->end();
        }
    }

    /**
     * @return array<int, string>
     */
    public function tags(): array
    {
        return $this->tenantId === null ? [] : ["tenant:{$this->tenantId}"];
    }
}
