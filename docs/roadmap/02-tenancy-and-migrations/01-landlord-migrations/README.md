# Task: Landlord Migrations

## Cel

Przygotować migracje public schema dla danych systemowych: tenants, domains, features, plans, subscriptions, licenses i global identities.

## Zakres

- Katalog `database/migrations/landlord`.
- Komenda `landlord:migrate`.
- Tabele landlord bez danych biznesowych tenantów.

## Poza Zakresem

- Migracje tenantowe.
- Seedowanie planów produkcyjnych.

## Zależności

- PostgreSQL connection.
- Schema manager.

## Kroki

- Uzupełnić landlord migration path.
- Dodać bazowe tabele systemowe.
- Upewnić się, że `search_path` wskazuje `public`.

## Subtaski

- [Create Landlord Migration Path](subtasks/01-create-landlord-migration-path.md)
- [Create System Tables](subtasks/02-create-system-tables.md)

## Acceptance Criteria

- `landlord:migrate` tworzy tabele w `public`.
- Landlord migracje nie tworzą tabel tenantowych.
- Główne tabele mają soft deletes, jeśli użytkownik/system może je usuwać.

## Test Plan

- `php artisan landlord:migrate --force`
- SQL: `SELECT to_regclass('public.tenants');`
