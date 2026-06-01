<?php

namespace App\Services\Tenancy;

use Illuminate\Support\Facades\DB;

final class PostgresSchemaManager
{
    public function create(string $schema): void
    {
        if (! $this->usesPostgres()) {
            return;
        }

        DB::statement(sprintf(
            'CREATE SCHEMA IF NOT EXISTS %s',
            $this->quoteIdentifier($schema),
        ));
    }

    public function setSearchPath(string $schema): void
    {
        if (! $this->usesPostgres()) {
            return;
        }

        DB::statement(sprintf(
            'SET search_path TO %s, public',
            $this->quoteIdentifier($schema),
        ));
    }

    public function usePublicSchema(): void
    {
        if (! $this->usesPostgres()) {
            return;
        }

        DB::statement('SET search_path TO public');
    }

    public function resetSearchPath(): void
    {
        if (! $this->usesPostgres()) {
            return;
        }

        DB::statement('RESET search_path');
    }

    public function quoteIdentifier(string $identifier): string
    {
        return '"'.str_replace('"', '""', $identifier).'"';
    }

    public function usesPostgres(): bool
    {
        return DB::connection()->getDriverName() === 'pgsql';
    }
}
