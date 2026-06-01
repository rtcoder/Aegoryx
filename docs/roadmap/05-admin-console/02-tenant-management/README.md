# Task: Tenant Management

## Status

Done.

## Cel

Pozwolić superadminowi tworzyć, przeglądać i zarządzać tenantami.

## Zakres

- Lista tenantów.
- Szczegóły tenanta.
- Status active/suspended/deleted.
- Link do support mode.

## Poza Zakresem

- Billing checkout.
- Tenant self-service signup.

## Zależności

- Tenant creation flow.
- Admin navigation.

## Kroki

- Dodać controller/request/resource.
- Przenieść logikę tworzenia do Action.
- Audytować zmiany statusu.

## Subtaski

Brak.

## Acceptance Criteria

- Tenant management nie edytuje tenant business data.
- Status change zapisuje audit.
- Schema name nie jest edytowana raw inputem.

## Test Plan

- Feature tests list/create/update status.
- Audit tests.
