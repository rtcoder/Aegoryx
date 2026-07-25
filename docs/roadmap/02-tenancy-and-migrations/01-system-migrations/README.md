# Task: System Migrations

## Status

Done.

## Cel

Przygotować migracje public schema dla danych systemowych: tenants, domains, features, plans, subscriptions, licenses i global identities.

## Zakres

- Katalog `database/migrations/system`.
- Komenda `system:migrate`.
- Tabele system bez danych biznesowych tenantów.

## Poza Zakresem

- Migracje tenantowe.
- Seedowanie planów produkcyjnych.

## Zależności

- PostgreSQL connection.
- Schema manager.

## Kroki

- Uzupełnić system migration path.
- Dodać bazowe tabele systemowe.
- Upewnić się, że `search_path` wskazuje `public`.

## Subtaski

- [Create System Migration Path](subtasks/01-create-system-migration-path.md)
- [Create System Tables](subtasks/02-create-system-tables.md)

## Acceptance Criteria

- `system:migrate` tworzy tabele w `public`.
- System migracje nie tworzą tabel tenantowych.
- Główne tabele mają soft deletes, jeśli użytkownik/system może je usuwać.

## Test Plan

- `php artisan system:migrate --force`
- SQL: `SELECT to_regclass('public.tenants');`
