# Task: Feature Management

## Status

Done.

## Cel

Zarządzać dostępem tenantów do funkcji systemowych zdefiniowanych w enumie kodu.

## Zakres

- Lista funkcji pochodzi z enuma aplikacji.
- Tenant feature overrides.
- Reason dla zmian.

## Poza Zakresem

- Billing provider plans.

## Zależności

- Entitlement keys.
- Manual overrides.

## Kroki

- Wyświetlić funkcje systemowe na ekranie tenanta.
- Dodać akcje enable/disable override.
- Zapisać audit.

## Subtaski

Brak.

## Acceptance Criteria

- Zmiany dostępu przechodzą przez Entitlements.
- Każda zmiana ma actor i reason.
- CMS/CRM nie sprawdzają admin tables bezpośrednio.

## Test Plan

- Feature tests enable/disable.
- Unit tests entitlement result.
