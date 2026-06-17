# Task: Plan Limits

## Status

Done for effective entitlement resolution.

## Implemented Notes

- `Plan` ma relację `features`, a aktywna/trialowa subskrypcja może zasilać `EffectiveEntitlements`.
- Limity planu są dostępne przez `EffectiveEntitlements::limit`.
- Konfiguracja per feature jest dostępna przez `EffectiveEntitlements::config`.

## Cel

Modelować limity planów SaaS w sposób konsumowany przez Entitlements.

## Zakres

- Plan model.
- Limit values.
- Effective limits per tenant.

## Poza Zakresem

- Płatności.
- License verification.

## Zależności

- Entitlement keys.
- Landlord migrations.

## Kroki

- Dodać tabele planów i limitów.
- Zmapować plan na effective entitlements.
- Dodać testy granic limitów.

## Subtaski

Brak.

## Acceptance Criteria

- Limit jest pobierany przez Entitlements.
- Brak direct plan checks w CMS/CRM.
- Override może zmienić limit.

## Test Plan

- Unit tests `limit`.
- Feature tests limit enforcement.
