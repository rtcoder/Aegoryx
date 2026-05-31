# Task: Tenant Migrations

## Cel

Przygotować migracje tenantowe wykonywane w każdej tenant schema z osobną tabelą `migrations`.

## Zakres

- Katalog `database/migrations/tenant`.
- Komenda `tenants:migrate`.
- Tenant tables bez `tenant_id`, jeśli tabela jest stricte tenantowa.

## Poza Zakresem

- Landlord migrations.
- Tenant creation UI.

## Zależności

- Landlord `tenants`.
- Schema manager.

## Kroki

- Ustawić `search_path` przed migracją każdego tenanta.
- Uruchomić `migrate --path=database/migrations/tenant`.
- Resetować `search_path` po każdej próbie.

## Subtaski

- [Separate Migrations Table Per Schema](subtasks/01-separate-migrations-table-per-schema.md)

## Acceptance Criteria

- Każdy tenant ma własną tabelę `migrations`.
- Tenant tables nie powstają w `public`.
- Błąd jednego tenanta zatrzymuje proces albo respektuje `--continue-on-error`.

## Test Plan

- Migracja dwóch tenantów testowych.
- SQL: `SELECT to_regclass('tenant_1.migrations');`
- SQL: `SELECT to_regclass('public.crm_contacts');`
