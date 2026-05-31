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

- Tworzyć minimum dwóch tenantów w testach.
- Wstawiać podobne dane w obu schemach.
- Sprawdzać brak cross-tenant reads.

## Subtaski

Brak.

## Acceptance Criteria

- Każdy tenant module ma isolation test.
- Cache nie miesza tenantów.
- Public API respektuje domain tenant resolving.

## Test Plan

- `php artisan test --filter=TenantIsolation`
