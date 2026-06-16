<?php

namespace Tests\Feature\Tenancy;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Throwable;

final class TenantMigrationIsolationTest extends TestCase
{
    private const CONNECTION = 'pgsql_tenancy_test';

    /** @var list<string> */
    private array $schemas = [];

    private string $defaultConnection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->defaultConnection = config('database.default');

        $this->configurePostgresConnection();
        $this->ensurePostgresIsAvailable();
    }

    protected function tearDown(): void
    {
        foreach (array_reverse($this->schemas) as $schema) {
            DB::connection(self::CONNECTION)->statement(sprintf(
                'DROP SCHEMA IF EXISTS %s CASCADE',
                $this->quoteIdentifier($schema),
            ));
        }

        DB::purge(self::CONNECTION);
        config(['database.default' => $this->defaultConnection]);

        parent::tearDown();
    }

    public function test_tenant_migrations_create_tables_inside_each_tenant_schema(): void
    {
        $firstSchema = $this->createTenantSchema();
        $secondSchema = $this->createTenantSchema();

        $this->migrateTenantSchema($firstSchema);
        $this->migrateTenantSchema($secondSchema);

        $this->assertSame("{$firstSchema}.migrations", $this->regclass("{$firstSchema}.migrations"));
        $this->assertSame("{$secondSchema}.migrations", $this->regclass("{$secondSchema}.migrations"));
        $this->assertNotSame(
            $this->tableOid("{$firstSchema}.migrations"),
            $this->tableOid("{$secondSchema}.migrations"),
        );
    }

    public function test_tenant_migrations_do_not_create_tenant_tables_in_public_schema(): void
    {
        $schema = $this->createTenantSchema();

        $this->migrateTenantSchema($schema);

        foreach (['users', 'activity_entries', 'cms_pages', 'cms_page_revisions', 'published_pages', 'crm_contacts', 'crm_companies', 'crm_deals'] as $table) {
            $this->assertNull(
                $this->regclass("public.{$table}"),
                "Tenant table [{$table}] exists in public schema.",
            );
        }
    }

    private function configurePostgresConnection(): void
    {
        config([
            'database.default' => self::CONNECTION,
            'database.connections.'.self::CONNECTION => [
                'driver' => 'pgsql',
                'host' => env('TENANCY_PGSQL_HOST', '127.0.0.1'),
                'port' => env('TENANCY_PGSQL_PORT', '5432'),
                'database' => env('TENANCY_PGSQL_DATABASE', 'aegoryx'),
                'username' => env('TENANCY_PGSQL_USERNAME', 'postgres'),
                'password' => env('TENANCY_PGSQL_PASSWORD', ''),
                'charset' => 'utf8',
                'prefix' => '',
                'prefix_indexes' => true,
                'search_path' => 'public',
                'sslmode' => env('TENANCY_PGSQL_SSLMODE', 'prefer'),
            ],
        ]);

        DB::purge(self::CONNECTION);
    }

    private function ensurePostgresIsAvailable(): void
    {
        try {
            DB::connection(self::CONNECTION)->getPdo();
        } catch (Throwable $exception) {
            $this->markTestSkipped('PostgreSQL tenancy test connection is not available: '.$exception->getMessage());
        }
    }

    private function createTenantSchema(): string
    {
        $schema = 'tenant_test_'.strtolower(str()->random(12));
        $this->schemas[] = $schema;

        DB::connection(self::CONNECTION)->statement(sprintf(
            'CREATE SCHEMA %s',
            $this->quoteIdentifier($schema),
        ));

        return $schema;
    }

    private function migrateTenantSchema(string $schema): void
    {
        DB::connection(self::CONNECTION)->statement(sprintf(
            'SET search_path TO %s, public',
            $this->quoteIdentifier($schema),
        ));

        $exitCode = Artisan::call('migrate', [
            '--database' => self::CONNECTION,
            '--path' => 'database/migrations/tenant',
            '--force' => true,
        ]);

        DB::connection(self::CONNECTION)->statement('RESET search_path');

        $this->assertSame(0, $exitCode, Artisan::output());
    }

    private function regclass(string $table): ?string
    {
        return DB::connection(self::CONNECTION)
            ->selectOne('select to_regclass(?) as table_name', [$table])
            ?->table_name;
    }

    private function tableOid(string $table): ?int
    {
        $oid = DB::connection(self::CONNECTION)
            ->selectOne('select to_regclass(?)::oid as table_oid', [$table])
            ?->table_oid;

        return $oid === null ? null : (int) $oid;
    }

    private function quoteIdentifier(string $identifier): string
    {
        return '"'.str_replace('"', '""', $identifier).'"';
    }
}
