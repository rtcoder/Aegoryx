<?php

namespace App\Modules\Tenancy\Services;

final class NativeDnsTxtResolver implements DnsTxtResolver
{
    public function records(string $host): array
    {
        $records = dns_get_record($host, DNS_TXT);

        if ($records === false) {
            return [];
        }

        return array_values(array_filter(array_map(
            fn (array $record): ?string => isset($record['txt']) ? (string) $record['txt'] : null,
            $records,
        )));
    }
}
