# Task: Feature Management

## Cel

Zarządzać globalnymi features i przypisaniami feature access dla tenantów.

## Zakres

- Feature registry UI.
- Tenant feature overrides.
- Reason dla zmian.

## Poza Zakresem

- Billing provider plans.

## Zależności

- Entitlement keys.
- Manual overrides.

## Kroki

- Wyświetlić global features.
- Dodać akcje enable/disable override.
- Zapisać audit.

## Subtaski

Brak.

## Acceptance Criteria

- Feature changes przechodzą przez Entitlements.
- Każda zmiana ma actor i reason.
- CMS/CRM nie sprawdzają admin tables bezpośrednio.

## Test Plan

- Feature tests enable/disable.
- Unit tests entitlement result.
