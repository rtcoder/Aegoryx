# Task: Feature Management

## Status

Done.

## Implemented Notes

- Panel tenanta pokazuje effective entitlement state per feature: enabled/disabled, source i reason.
- Formularz startuje od `EffectiveEntitlements`, więc plan/licencja nie są przypadkowo prezentowane jako wyłączone.
- Zapis funkcji tworzy manual override tylko dla wartości zmienionych względem effective state.
- Manual override można wyczyścić, żeby wrócić do decyzji planu/licencji/systemu.
- Funkcje systemowe są definiowane w `SystemFeature`, nie w tabeli `features`.
- Tenant access jest zapisywany jako manual override w `tenant_features`.
- Zmiany manual override są audytowane.

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
- Panel pokazuje źródło efektywnej decyzji przed zapisem override.
- Clear manual override usuwa wpis i zapisuje audit.
- CMS/CRM nie sprawdzają admin tables bezpośrednio.

## Test Plan

- Feature tests enable/disable.
- Unit tests entitlement result.
