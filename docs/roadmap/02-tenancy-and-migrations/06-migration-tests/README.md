# Task: Migration Tests

## Status

Done.

## Cel

Potwierdzić, że migracje system/tenant działają w schema-per-tenant i nie mieszają danych.

## Zakres

- Testy system migrations.
- Testy tenant migrations dla minimum dwóch tenantów.
- Test osobnej tabeli `migrations` per schema.
- Test resetu tenant context.

## Poza Zakresem

- Pełne testy modułów CMS/CRM.

## Zależności

- Działający PostgreSQL w testach.
- Tenant creation helpers.

## Kroki

- Przygotować helper tworzenia schem testowych.
- Uruchomić system migrations.
- Uruchomić tenant migrations dla dwóch tenantów.
- Sprawdzić `to_regclass`.

## Subtaski

- [Assert No Tenant Tables In Public](subtasks/01-assert-no-tenant-tables-in-public.md)

## Acceptance Criteria

- Testy łapią stworzenie tenant table w `public`.
- Testy łapią wspólną tabelę `public.migrations` dla tenantów.
- Testy potwierdzają izolację danych.

## Test Plan

- `php artisan test --filter=Migration`
