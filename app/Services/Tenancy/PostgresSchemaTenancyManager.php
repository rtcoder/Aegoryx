<?php

namespace App\Services\Tenancy;

use App\Models\Landlord\Tenant;
use Illuminate\Support\Facades\DB;

final class PostgresSchemaTenancyManager implements TenancyManager
{
    private ?Tenant $tenant = null;

    public function initialize(Tenant $tenant): void
    {
        $this->tenant = $tenant;

        DB::statement(sprintf(
            'SET search_path TO %s, public',
            $this->quoteIdentifier($tenant->schema_name),
        ));
    }

    public function end(): void
    {
        DB::statement('RESET search_path');

        $this->tenant = null;
    }

    public function current(): ?Tenant
    {
        return $this->tenant;
    }

    private function quoteIdentifier(string $identifier): string
    {
        return '"'.str_replace('"', '""', $identifier).'"';
    }
}
