<?php

namespace App\Services\Tenancy;

use Illuminate\Support\Facades\DB;

final class PostgresSchemaManager
{
    public function create(string $schema): void
    {
        DB::statement(sprintf(
            'CREATE SCHEMA IF NOT EXISTS %s',
            $this->quoteIdentifier($schema),
        ));
    }

    public function setSearchPath(string $schema): void
    {
        DB::statement(sprintf(
            'SET search_path TO %s, public',
            $this->quoteIdentifier($schema),
        ));
    }

    public function usePublicSchema(): void
    {
        DB::statement('SET search_path TO public');
    }

    public function resetSearchPath(): void
    {
        DB::statement('RESET search_path');
    }

    public function quoteIdentifier(string $identifier): string
    {
        return '"'.str_replace('"', '""', $identifier).'"';
    }
}
