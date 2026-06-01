# Task: Tenancy Manager

## Status

Done.

## Cel

Zapewnić centralny mechanizm inicjalizacji i resetowania tenant context.

## Zakres

- `TenancyManager` interface.
- Implementacja PostgreSQL schema-per-tenant.
- Aktualny tenant w procesie.

## Poza Zakresem

- Tenant resolving z requestu.
- Database-per-tenant implementation.

## Zależności

- `Tenant` model.
- Schema manager.

## Kroki

- Zaimplementować `initialize(Tenant $tenant)`.
- Zaimplementować `end()`.
- Wymusić reset w jobach, commands i middleware.

## Subtaski

- [Reset Search Path Safely](subtasks/01-reset-search-path-safely.md)

## Acceptance Criteria

- Tenant context nie jest ustawiany w kontrolerach ani modelach.
- `end()` resetuje `search_path`.
- Long-running processes nie dziedziczą starego tenanta.

## Test Plan

- Test `initialize` ustawia tenant schema.
- Test `end` resetuje context.
