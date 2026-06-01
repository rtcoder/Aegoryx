# Task: Schema Manager

## Status

Done.

## Cel

Wyodrębnić bezpieczne operacje PostgreSQL schema, w tym tworzenie schem i cytowanie identyfikatorów.

## Zakres

- `PostgresSchemaManager`.
- `CREATE SCHEMA IF NOT EXISTS`.
- `SET search_path`.
- `RESET search_path`.

## Poza Zakresem

- Logika biznesowa tworzenia tenantów.
- Migracje database-per-tenant.

## Zależności

- PostgreSQL connection.

## Kroki

- Używać bezpiecznego quote identifier.
- Nigdy nie składać SQL z raw user input.
- Udostępnić metody używane przez komendy migracyjne.

## Subtaski

Brak.

## Acceptance Criteria

- Schema name jest zawsze cytowana.
- Manager nie zna HTTP requestu.
- Manager nie wykonuje migracji samodzielnie.

## Test Plan

- Unit/integration test cytowania identyfikatorów.
- Test tworzenia schemy w PostgreSQL.
