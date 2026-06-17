# Task: Entitlement Keys

## Status

Done.

## Implemented Notes

- `SystemFeature` jest centralnym registry funkcji produktu.
- Business modules używają `EffectiveEntitlements`, a nie bezpośrednio billing/licensing.
- API obejmuje `allows`, `limit` i `config`.

## Cel

Zdefiniować spójne klucze features, limitów i konfiguracji, których używają moduły.

## Zakres

- Naming convention.
- Registry entitlementów.
- API `allows`, `limit`, `config`.

## Poza Zakresem

- Integracja z Paddle.
- License server.

## Zależności

- Lista modułów produktowych.

## Kroki

- Spisać pierwsze klucze CMS/CRM/Public API.
- Dodać centralny registry.
- Udokumentować format nazw.

## Subtaski

Brak.

## Acceptance Criteria

- Moduły nie sprawdzają billing bezpośrednio.
- Klucze są stabilne i opisane.
- Brak magic strings poza registry.

## Test Plan

- Unit tests registry.
