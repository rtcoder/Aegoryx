<?php

namespace App\Services\Tenancy;

use App\Models\System\Tenant;

interface TenancyManager
{
    public function initialize(Tenant $tenant): void;

    public function end(): void;

    public function current(): ?Tenant;
}
