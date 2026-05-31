# Task: License Verification

## Cel

Obsłużyć self-hosted license verification dla subscription i perpetual licenses.

## Zakres

- License model.
- License payload validation.
- Expiry/grace state.
- Audit zmian licencji.

## Poza Zakresem

- Budowa zewnętrznego license servera.

## Zależności

- Entitlements.
- Security logging.

## Kroki

- Zdefiniować format wewnętrznego license state.
- Weryfikować podpis/payload.
- Mapować state na entitlementy.

## Subtaski

Brak.

## Acceptance Criteria

- Business modules nie czytają license payload.
- Expired license odbiera właściwe features.
- Perpetual license działa bez subskrypcji SaaS.

## Test Plan

- Unit tests valid/expired/grace/perpetual.
