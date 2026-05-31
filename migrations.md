# migrations.md — Aegoryx multi-schema migration strategy

## 1. Purpose

This document defines the migration strategy for **Aegoryx**, a privacy-first CMS + CRM platform using **Laravel + PostgreSQL schema-per-tenant multi-tenancy**.

The goal is to make Codex implement database migrations safely, predictably, and in a way that keeps future migration from `schema per tenant` to `database per tenant` possible.

---

## 2. Core decision

Aegoryx uses:

```txt
PostgreSQL schema per tenant
```

not:

```txt
single database with tenant_id everywhere
```

and not initially:

```txt
database per tenant
```

This means one physical PostgreSQL database contains:

```txt
public                  -> landlord/system schema
tenant_acme             -> tenant business schema
tenant_demo             -> tenant business schema
tenant_client_x         -> tenant business schema
```

Example structure:

```txt
public.tenants
public.tenant_domains
public.features
public.plans
public.subscriptions
public.licenses
public.system_installations
public.identities

 tenant_acme.users
 tenant_acme.cms_pages
 tenant_acme.crm_contacts
 tenant_acme.files
 tenant_acme.audit_logs
 tenant_acme.migrations

 tenant_demo.users
 tenant_demo.cms_pages
 tenant_demo.crm_contacts
 tenant_demo.files
 tenant_demo.audit_logs
 tenant_demo.migrations
```

---

## 3. Migration categories

There are two migration categories:

```txt
1. Landlord migrations
2. Tenant migrations
```

### 3.1 Landlord migrations

Landlord migrations create and modify tables in the `public` schema.

Landlord data includes:

- tenants,
- tenant domains,
- features,
- plans,
- subscriptions,
- licenses,
- system installations,
- global identities,
- superadmin data,
- global audit data.

### 3.2 Tenant migrations

Tenant migrations create and modify tables in each tenant schema.

Tenant data includes:

- tenant users / memberships,
- CMS data,
- CRM data,
- files metadata,
- tenant audit logs,
- tenant-specific settings.

---

## 4. Migration folder structure

Use separate folders:

```txt
database/migrations/landlord
  2026_01_01_000001_create_tenants_table.php
  2026_01_01_000002_create_tenant_domains_table.php
  2026_01_01_000003_create_features_table.php
  2026_01_01_000004_create_plans_table.php
  2026_01_01_000005_create_subscriptions_table.php
  2026_01_01_000006_create_licenses_table.php

database/migrations/tenant
  2026_01_01_000001_create_users_table.php
  2026_01_01_000002_create_roles_table.php
  2026_01_01_000003_create_cms_pages_table.php
  2026_01_01_000004_create_cms_page_revisions_table.php
  2026_01_01_000005_create_published_pages_table.php
  2026_01_01_000006_create_crm_contacts_table.php
  2026_01_01_000007_create_crm_companies_table.php
  2026_01_01_000008_create_crm_deals_table.php
  2026_01_01_000009_create_files_table.php
  2026_01_01_000010_create_audit_logs_table.php
```

Do not put landlord and tenant migrations in the same folder.

Do not rely on default `database/migrations` for this project.

---

## 5. Artisan commands

Implement explicit migration commands.

Required commands:

```bash
php artisan landlord:migrate
php artisan tenants:migrate
php artisan tenant:migrate {tenant}
php artisan tenants:rollback
php artisan tenant:rollback {tenant}
php artisan tenants:fresh --env=local
```

Optional project-prefixed aliases:

```bash
php artisan aegoryx:migrate-landlord
php artisan aegoryx:migrate-tenants
php artisan aegoryx:migrate-tenant {tenant}
```

Recommended deploy commands:

```bash
php artisan down
php artisan landlord:migrate --force
php artisan tenants:migrate --force
php artisan optimize:clear
php artisan optimize
php artisan up
```

---

## 6. Meaning of plain `php artisan migrate`

Plain Laravel command:

```bash
php artisan migrate
```

must not be used as the production command for the entire system.

Recommended rule:

```txt
php artisan migrate              -> landlord only, or disabled with a clear warning
php artisan landlord:migrate     -> public schema only
php artisan tenants:migrate      -> all tenant schemas
php artisan tenant:migrate acme  -> one tenant schema
```

Codex must not implement logic that assumes plain `php artisan migrate` automatically migrates all tenant schemas.

---

## 7. PostgreSQL search_path strategy

Tenant migrations depend on PostgreSQL `search_path`.

For landlord migrations:

```sql
SET search_path TO public;
```

For tenant migrations:

```sql
SET search_path TO tenant_acme, public;
```

With this search path, a migration like:

```php
Schema::create('crm_contacts', function (Blueprint $table) {
    $table->id();
    $table->timestamps();
});
```

creates:

```txt
tenant_acme.crm_contacts
```

not:

```txt
public.crm_contacts
```

### 7.1 Rules

- Never set `search_path` manually in controllers.
- Never set `search_path` manually in models.
- Never set `search_path` manually inside random services.
- Only the Tenancy Manager, migration commands, queue bootstrap, and test helpers may set tenant context.
- Always reset search path after tenant-scoped work.

Use:

```sql
RESET search_path;
```

or set it back explicitly:

```sql
SET search_path TO public;
```

---

## 8. Migrations table strategy

Each schema must have its own `migrations` table.

Correct:

```txt
public.migrations
 tenant_acme.migrations
 tenant_demo.migrations
 tenant_client_x.migrations
```

Wrong:

```txt
public.migrations only for all tenant migrations
```

Reason:

If all tenant migrations use `public.migrations`, Laravel will mark a tenant migration as already executed after the first tenant and will skip it for the next tenants.

Therefore, before running tenant migrations, the active `search_path` must point to the target tenant schema.

---

## 9. Landlord migration command

Implement a command similar to this.

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

final class MigrateLandlordCommand extends Command
{
    protected $signature = 'landlord:migrate {--force : Force migrations in production}';

    protected $description = 'Run landlord migrations on the public schema.';

    public function handle(): int
    {
        DB::statement('SET search_path TO public');

        $exitCode = Artisan::call('migrate', [
            '--path' => 'database/migrations/landlord',
            '--database' => 'pgsql',
            '--force' => (bool) $this->option('force'),
        ]);

        $this->output->write(Artisan::output());

        return $exitCode;
    }
}
```

---

## 10. Tenant migration command

Implement a command similar to this.

```php
<?php

namespace App\Console\Commands;

use App\Models\Landlord\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Throwable;

final class MigrateTenantsCommand extends Command
{
    protected $signature = 'tenants:migrate
        {--tenant= : Tenant ID or slug}
        {--force : Force migrations in production}
        {--continue-on-error : Continue migrating other tenants if one tenant fails}';

    protected $description = 'Run tenant migrations for all tenant schemas or one selected tenant.';

    public function handle(): int
    {
        $query = Tenant::query()
            ->where('status', '!=', 'deleted');

        if ($tenant = $this->option('tenant')) {
            $query->where(function ($query) use ($tenant): void {
                $query
                    ->where('id', $tenant)
                    ->orWhere('slug', $tenant);
            });
        }

        $tenants = $query->orderBy('id')->get();

        if ($tenants->isEmpty()) {
            $this->warn('No tenants found.');

            return self::SUCCESS;
        }

        $failed = [];

        foreach ($tenants as $tenant) {
            $schema = $tenant->schema_name;

            $this->info("Migrating tenant [{$tenant->id}] [{$tenant->slug}] schema [{$schema}]...");

            try {
                $this->ensureSchemaExists($schema);
                $this->setSearchPath($schema);

                $exitCode = Artisan::call('migrate', [
                    '--path' => 'database/migrations/tenant',
                    '--database' => 'pgsql',
                    '--force' => (bool) $this->option('force'),
                ]);

                $this->output->write(Artisan::output());

                if ($exitCode !== self::SUCCESS) {
                    throw new \RuntimeException("Migration failed for tenant [{$tenant->id}] with exit code [{$exitCode}].");
                }
            } catch (Throwable $exception) {
                $failed[] = [
                    'tenant_id' => $tenant->id,
                    'schema' => $schema,
                    'error' => $exception->getMessage(),
                ];

                $this->error("Tenant [{$tenant->id}] migration failed: {$exception->getMessage()}");

                if (! $this->option('continue-on-error')) {
                    return self::FAILURE;
                }
            } finally {
                DB::statement('RESET search_path');
            }
        }

        if ($failed !== []) {
            $this->error('Some tenant migrations failed.');

            foreach ($failed as $failure) {
                $this->line("- Tenant {$failure['tenant_id']} / {$failure['schema']}: {$failure['error']}");
            }

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function ensureSchemaExists(string $schema): void
    {
        DB::statement(sprintf(
            'CREATE SCHEMA IF NOT EXISTS %s',
            $this->quoteIdentifier($schema),
        ));
    }

    private function setSearchPath(string $schema): void
    {
        DB::statement(sprintf(
            'SET search_path TO %s, public',
            $this->quoteIdentifier($schema),
        ));
    }

    private function quoteIdentifier(string $identifier): string
    {
        return '"' . str_replace('"', '""', $identifier) . '"';
    }
}
```

---

## 11. Single tenant alias command

This command may simply call `tenants:migrate --tenant=...`.

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

final class MigrateTenantCommand extends Command
{
    protected $signature = 'tenant:migrate
        {tenant : Tenant ID or slug}
        {--force : Force migrations in production}';

    protected $description = 'Run tenant migrations for a single tenant.';

    public function handle(): int
    {
        return Artisan::call('tenants:migrate', [
            '--tenant' => $this->argument('tenant'),
            '--force' => (bool) $this->option('force'),
        ]);
    }
}
```

---

## 12. Tenant creation flow

When creating a tenant, do this:

```txt
1. Insert tenant row into public.tenants.
2. Generate safe schema name.
3. Create PostgreSQL schema.
4. Run tenant migrations for that schema.
5. Run tenant seeders/default setup.
6. Create owner membership.
7. Create default features/settings.
```

Example command:

```bash
php artisan tenants:create acme
```

Internal flow:

```txt
INSERT INTO public.tenants (..., schema_name = 'tenant_acme')
CREATE SCHEMA tenant_acme
SET search_path TO tenant_acme, public
php artisan migrate --path=database/migrations/tenant
php artisan db:seed --class=TenantDatabaseSeeder
RESET search_path
```

---

## 13. Schema naming rules

Tenant schema names must be generated by the application.

Do not use raw user input as schema name.

Recommended format:

```txt
tenant_{id}
```

or:

```txt
tenant_{safe_slug}
```

Preferred:

```txt
tenant_{id}
```

because it avoids rename issues when tenant slug changes.

Examples:

```txt
tenant_1
tenant_42
tenant_1042
```

If using slug-based schemas, sanitize strictly:

```txt
lowercase letters
numbers
underscore only
must start with tenant_
```

Never allow:

```txt
spaces
quotes
semicolons
dots
dash if not explicitly supported
untrusted raw strings
```

Always quote identifiers in raw schema SQL.

---

## 14. Example landlord migration

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('schema_name')->unique();
            $table->string('status')->default('active');
            $table->string('deployment_type')->default('saas');
            $table->string('billing_model')->default('subscription');
            $table->string('license_type')->default('saas_subscription');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
```

This migration belongs in:

```txt
database/migrations/landlord
```

---

## 15. Example tenant migration

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_contacts', function (Blueprint $table): void {
            $table->id();

            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();

            $table->text('email_encrypted')->nullable();
            $table->string('email_hash')->nullable()->index();

            $table->text('phone_encrypted')->nullable();
            $table->string('phone_hash')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_contacts');
    }
};
```

This migration belongs in:

```txt
database/migrations/tenant
```

It must not include `tenant_id` unless there is a specific technical reason.

---

## 16. Model rules

Use separate model namespaces:

```txt
App\Models\Landlord\Tenant
App\Models\Landlord\Feature
App\Models\Landlord\Plan
App\Models\Tenant\CrmContact
App\Models\Tenant\CmsPage
```

Landlord models use the landlord/public connection/context.

Tenant models require initialized tenant context.

Tenant models must not manually set schema-qualified table names like:

```php
protected $table = 'tenant_acme.crm_contacts';
```

Wrong.

Tenant models should use normal table names:

```php
protected $table = 'crm_contacts';
```

The active `search_path` decides the schema.

---

## 17. Rollback strategy

Rollback must also be schema-aware.

Commands:

```bash
php artisan landlord:rollback
php artisan tenants:rollback
php artisan tenant:rollback acme
```

Tenant rollback flow:

```txt
foreach tenant:
    SET search_path TO tenant_schema, public
    php artisan migrate:rollback --path=database/migrations/tenant
    RESET search_path
```

Production rule:

```txt
Prefer forward-only corrective migrations over rollback.
```

Rollback is acceptable in local/dev.

Rollback in production must require:

- backup,
- explicit `--force`,
- clear operator confirmation,
- maintenance mode if needed.

---

## 18. Fresh/reset strategy

Never run tenant fresh/reset in production.

Allowed in local development:

```bash
php artisan landlord:fresh --env=local
php artisan tenants:fresh --env=local
```

Tenant fresh should:

```txt
1. Drop all tenant schemas.
2. Recreate tenant schemas.
3. Run tenant migrations.
4. Run tenant seeders.
```

Protect this with environment checks.

Hard rule:

```php
if (! app()->environment('local', 'testing')) {
    throw new RuntimeException('This command is only allowed in local/testing.');
}
```

---

## 19. Queue jobs and migrations

Migrations are not the only place where tenant context matters.

Every tenant job must carry `tenant_id`.

Wrong:

```php
ImportContactsJob::dispatch($fileId);
```

Correct:

```php
ImportContactsJob::dispatch($tenantId, $fileId);
```

Job flow:

```php
public function handle(): void
{
    $tenant = Tenant::query()->findOrFail($this->tenantId);

    Tenancy::initialize($tenant);

    try {
        // Work on tenant tables.
    } finally {
        Tenancy::end();
    }
}
```

This is relevant to migrations because queue workers keep database connections alive. Always reset tenant context after tenant work.

---

## 20. Database connection safety

When switching schemas on a reused DB connection, always reset context.

Required pattern:

```php
Tenancy::initialize($tenant);

try {
    // tenant work
} finally {
    Tenancy::end();
}
```

`Tenancy::end()` should reset the `search_path`.

For long-running processes such as queue workers, scheduled commands, websocket workers, or daemons, never assume a clean DB connection.

---

## 21. Testing requirements

Create tests for migration behavior.

Required tests:

```txt
- landlord:migrate creates public.tenants.
- tenants:migrate creates crm_contacts in every tenant schema.
- tenants:migrate creates separate migrations table in every tenant schema.
- tenant:migrate acme migrates only tenant_acme.
- tenant_acme does not see tenant_demo data.
- tenant migrations do not create tenant tables in public.
- failed tenant migration can stop the process.
- --continue-on-error continues after a failed tenant.
- tenant context is reset after migration.
```

Useful SQL assertions:

```sql
SELECT to_regclass('public.tenants');
SELECT to_regclass('tenant_acme.crm_contacts');
SELECT to_regclass('tenant_demo.crm_contacts');
SELECT to_regclass('public.crm_contacts');
```

Expected:

```txt
public.tenants                  exists
tenant_acme.crm_contacts        exists
tenant_demo.crm_contacts        exists
public.crm_contacts             null / does not exist
```

---

## 22. CI strategy

CI should test at least:

```txt
1. landlord migrations
2. tenant migrations for at least two tenants
3. tenant isolation
4. tenant rollback in testing
```

Example CI flow:

```bash
php artisan landlord:migrate --force
php artisan tenants:create acme --no-interaction
php artisan tenants:create demo --no-interaction
php artisan tenants:migrate --force
php artisan test
```

For testing, create at least two tenant schemas.

Do not rely only on one tenant in tests; one tenant does not catch cross-tenant leakage.

---

## 23. Deployment strategy

Recommended production deployment:

```bash
php artisan down

php artisan landlord:migrate --force
php artisan tenants:migrate --force

php artisan optimize:clear
php artisan optimize

php artisan up
```

For large tenant counts, later add batching:

```bash
php artisan tenants:migrate --force --batch-size=25
```

Potential future options:

```bash
php artisan tenants:migrate --only-active
php artisan tenants:migrate --skip-suspended
php artisan tenants:migrate --tenant=acme
php artisan tenants:migrate --continue-on-error
```

---

## 24. Future database-per-tenant migration compatibility

Do not implement anything that makes future migration to `database per tenant` unnecessarily hard.

Rules:

```txt
- no cross-schema foreign keys
- no schema-qualified table names in models
- tenant context must be centralized
- jobs must carry tenant_id
- landlord and tenant migrations must be separate
- tenant code should not know whether tenant storage is schema or database
```

Future abstraction:

```php
interface TenantDatabaseManager
{
    public function initialize(Tenant $tenant): void;

    public function end(): void;

    public function migrate(Tenant $tenant): void;
}
```

Current implementation:

```txt
SchemaTenantDatabaseManager
```

Future implementation:

```txt
DatabaseTenantDatabaseManager
```

Business modules must not care which one is used.

---

## 25. Forbidden patterns

Do not do this:

```php
Schema::create('tenant_acme.crm_contacts', ...);
```

Do not do this:

```php
protected $table = 'tenant_acme.crm_contacts';
```

Do not do this:

```php
DB::statement('SET search_path TO ' . $request->tenant);
```

Do not do this:

```php
Contact::query()->where('tenant_id', $tenantId)->get();
```

if `Contact` is a tenant-schema model.

Do not create one global migrations table for tenant migrations.

Do not run tenant migrations from default `php artisan migrate` unless the command is explicitly tenant-aware.

Do not create cross-schema foreign keys from tenant schema to public schema.

---

## 26. Implementation checklist for Codex

When implementing this migration system, Codex should create or update:

```txt
1. database/migrations/landlord directory
2. database/migrations/tenant directory
3. Landlord migration command
4. Tenant migration command
5. Single tenant migration alias command
6. Tenant schema creation service
7. Tenancy manager / search_path manager
8. Tests for landlord migrations
9. Tests for tenant migrations
10. Tests for migration table per schema
11. Tests for tenant data isolation
12. Documentation in README or docs/migrations.md
```

Minimum service names:

```txt
App\Services\Tenancy\TenancyManager
App\Services\Tenancy\PostgresSchemaManager
App\Console\Commands\MigrateLandlordCommand
App\Console\Commands\MigrateTenantsCommand
App\Console\Commands\MigrateTenantCommand
```

---

## 27. Suggested TenancyManager interface

```php
<?php

namespace App\Services\Tenancy;

use App\Models\Landlord\Tenant;

interface TenancyManager
{
    public function initialize(Tenant $tenant): void;

    public function end(): void;

    public function current(): ?Tenant;
}
```

Suggested PostgreSQL implementation:

```php
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
        return '"' . str_replace('"', '""', $identifier) . '"';
    }
}
```

---

## 28. Summary

Aegoryx migrations must be explicit and tenant-aware.

Final rule set:

```txt
public schema       -> landlord migrations
 tenant schemas      -> tenant migrations
search_path         -> set centrally
migrations table    -> one per schema
plain migrate       -> not used for all tenants
jobs                -> always carry tenant_id
rollback            -> schema-aware and cautious
future db-per-tenant -> keep possible
```

Codex must prioritize safety over convenience.
