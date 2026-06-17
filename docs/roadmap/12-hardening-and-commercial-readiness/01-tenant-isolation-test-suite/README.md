# Task: Tenant Isolation Test Suite

## Cel

Zbudować szeroki zestaw testów potwierdzający izolację tenantów.

## Zakres

- CMS isolation.
- CRM isolation.
- Files isolation.
- Cache key isolation.

## Poza Zakresem

- Load testing.

## Zależności

- Główne moduły tenantowe.
- Migration tests.

## Kroki

- Tworzyć minimum dwóch tenantów/schema w testach PostgreSQL.
- Wstawiać podobne dane w obu schemach.
- Sprawdzać brak cross-tenant reads.
- Testować cache key public API per tenant.

## Subtaski

Brak.

## Acceptance Criteria

- [x] Każdy tenant module ma isolation test.
- [x] Cache nie miesza tenantów.
- [x] Public API respektuje domain tenant resolving.

## Test Plan

- [x] `php artisan test --filter=TenantIsolation`
