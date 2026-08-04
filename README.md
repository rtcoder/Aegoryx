# Aegoryx

Aegoryx is a privacy-first CMS and CRM platform built as a Laravel modular monolith. It supports SaaS and self-hosted deployments, PostgreSQL schema-per-tenant isolation, private CRM data, public read-only CMS APIs, audit trails, licensing, billing, and tenant entitlements.

## Local Requirements

- PHP 8.3 with PostgreSQL and Redis extensions available to Laravel.
- Composer 2.
- Node.js 22 or newer and npm.
- PostgreSQL 16 or newer.
- Redis 7 or newer for queues and Horizon.

PostgreSQL is the default local database. SQLite is only used by selected tests and should not be treated as the normal runtime.

## Local Environment

Copy the example environment file and generate an app key:

```bash
cp .env.example .env
php artisan key:generate
```

The default local domains are:

```txt
APP_URL=http://aegoryx.test
ADMIN_DOMAIN=admin.aegoryx.test
```

Add matching hosts entries if your local DNS does not resolve them:

```txt
127.0.0.1 aegoryx.test
127.0.0.1 admin.aegoryx.test
127.0.0.1 acme.aegoryx.test
```

The important local service defaults are:

```txt
DB_CONNECTION=pgsql
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=aegoryx
DB_USERNAME=postgres
DB_PASSWORD=
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

Create the local PostgreSQL database before running migrations:

```bash
createdb aegoryx
```

## Install Dependencies

```bash
composer install
npm install
```

Build frontend assets once for local verification:

```bash
npm run build
```

Use Vite while working on the UI:

```bash
npm run dev
```

## Migrations

Aegoryx separates system migrations from tenant migrations. Do not use plain `php artisan migrate` as the full project setup or deployment command.

Run system migrations first:

```bash
php artisan system:migrate --force
```

Create a local tenant when needed:

```bash
php artisan tenants:create "Acme" --domain=acme.aegoryx.test
```

Run tenant migrations after system migrations and after creating tenants:

```bash
php artisan tenants:migrate --force
```

For one tenant only:

```bash
php artisan tenant:migrate acme --force
```

## Running Locally

Start the Laravel server:

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Run workers during local feature work:

```bash
php artisan queue:work redis --queue=system,tenant,exports,default --tries=3
```

Run Horizon when testing queue supervision:

```bash
php artisan horizon
```

The Composer dev script starts the server, queue listener, logs, and Vite together:

```bash
composer run dev
```

## Verification

Check the local application environment:

```bash
php artisan about --only=environment
```

Check routes:

```bash
php artisan route:list
```

Run the test suite:

```bash
composer test
```

Run preflight checks without touching the database:

```bash
php artisan aegoryx:preflight --skip-db
```

## Quality Gates

Detailed coding rules live in `AGENT_CODING_GUIDELINES.md`. The short version for every change is:

- keep controllers thin and move business changes into Actions or Services,
- use DTOs for complex validated input, not for trivial one-field calls,
- use Query Objects for complex listing, filtering, and sorting,
- enforce authorization in policies, gates, or middleware, not only in UI,
- keep module boundaries clear and use Entitlements for feature access decisions,
- add or update tests for business behavior, tenant isolation, authorization, audit/activity history, and public API exposure,
- avoid abstractions that do not remove real duplication or clarify a boundary.

Minimum checks before a PR or direct push:

```bash
vendor/bin/pint --test
composer test
```

For frontend or design-system changes, also run:

```bash
npm run build
```

## Common Local Issues

- `could not find driver`: install or enable the PHP PostgreSQL extension used by your PHP CLI.
- database connection refused: start PostgreSQL and confirm `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD`.
- Redis connection refused: start Redis or temporarily use a non-Redis queue only for focused local debugging.
- admin login domain renders the tenant panel: confirm `ADMIN_DOMAIN=admin.aegoryx.test` and local hosts entries.
- tenant routes return 404: create a tenant domain and run tenant migrations.
- stale configuration: run `php artisan config:clear`.

## Project Workflow

Agent instructions live in `AGENT_CODING_GUIDELINES.md`.

Roadmap and backlog items live in YouTrack, not in repository roadmap files. Use the local `ytrack` CLI to inspect and update project work.
