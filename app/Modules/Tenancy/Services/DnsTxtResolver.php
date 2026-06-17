<?php

namespace App\Modules\Tenancy\Services;

interface DnsTxtResolver
{
    /**
     * @return array<int, string>
     */
    public function records(string $host): array;
}
