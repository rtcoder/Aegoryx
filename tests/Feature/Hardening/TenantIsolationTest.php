<?php

namespace Tests\Feature\Hardening;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Throwable;

final class TenantIsolationTest extends TestCase
{
    private const CONNECTION = 'pgsql_tenant_isolation_test';

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

    public function test_tenant_modules_do_not_read_data_across_postgres_schemas(): void
    {
        $firstSchema = $this->createTenantSchema();
        $secondSchema = $this->createTenantSchema();

        $this->migrateTenantSchema($firstSchema);
        $this->migrateTenantSchema($secondSchema);

        $this->usingSchema($firstSchema, function (): void {
            DB::connection(self::CONNECTION)->table('cms_pages')->insert([
                'title' => 'First CMS',
                'slug' => 'home',
                'status' => 'draft',
                'draft_content' => json_encode(['body' => 'first']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::connection(self::CONNECTION)->table('crm_contacts')->insert([
                'first_name' => 'Ada',
                'email_hash' => hash('sha256', 'ada@example.test'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::connection(self::CONNECTION)->table('files')->insert([
                'disk' => 'local',
                'path' => 'tenant-first/report.txt',
                'original_name' => 'report.txt',
                'size_bytes' => 10,
                'visibility' => 'private',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        $this->usingSchema($secondSchema, function (): void {
            DB::connection(self::CONNECTION)->table('cms_pages')->insert([
                'title' => 'Second CMS',
                'slug' => 'home',
                'status' => 'draft',
                'draft_content' => json_encode(['body' => 'second']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        $this->usingSchema($firstSchema, function (): void {
            $this->assertSame('First CMS', DB::connection(self::CONNECTION)->table('cms_pages')->value('title'));
            $this->assertSame('Ada', DB::connection(self::CONNECTION)->table('crm_contacts')->value('first_name'));
            $this->assertSame('tenant-first/report.txt', DB::connection(self::CONNECTION)->table('files')->value('path'));
        });

        $this->usingSchema($secondSchema, function (): void {
            $this->assertSame('Second CMS', DB::connection(self::CONNECTION)->table('cms_pages')->value('title'));
            $this->assertSame(0, DB::connection(self::CONNECTION)->table('crm_contacts')->count());
            $this->assertSame(0, DB::connection(self::CONNECTION)->table('files')->count());
        });
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
            $this->markTestSkipped('PostgreSQL tenant isolation test connection is not available: '.$exception->getMessage());
        }
    }

    private function createTenantSchema(): string
    {
        $schema = 'tenant_hardening_'.strtolower(str()->random(12));
        $this->schemas[] = $schema;

        DB::connection(self::CONNECTION)->statement(sprintf(
            'CREATE SCHEMA %s',
            $this->quoteIdentifier($schema),
        ));

        return $schema;
    }

    private function migrateTenantSchema(string $schema): void
    {
        $this->usingSchema($schema, function (): void {
            $exitCode = Artisan::call('migrate', [
                '--database' => self::CONNECTION,
                '--path' => 'database/migrations/tenant',
                '--force' => true,
            ]);

            $this->assertSame(0, $exitCode, Artisan::output());
        });
    }

    private function usingSchema(string $schema, callable $callback): void
    {
        DB::connection(self::CONNECTION)->statement(sprintf(
            'SET search_path TO %s, public',
            $this->quoteIdentifier($schema),
        ));

        try {
            $callback();
        } finally {
            DB::connection(self::CONNECTION)->statement('RESET search_path');
        }
    }

    private function quoteIdentifier(string $identifier): string
    {
        return '"'.str_replace('"', '""', $identifier).'"';
    }
}
