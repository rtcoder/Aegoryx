# Task: CORS Allow List

## Cel

Ograniczyć public API do zaufanych originów, jeśli tenant konfiguruje allow-list.

## Zakres

- Tenant/domain CORS config.
- Middleware.
- Safe defaults.

## Poza Zakresem

- Panel CORS.

## Zależności

- Public tenant resolving.
- Tenant settings.

## Kroki

- Dodać config allow-list.
- Sprawdzać origin per tenant.
- Testować allowed/denied.

## Subtaski

Brak.

## Acceptance Criteria

- CORS nie jest globalnie otwarty bez decyzji.
- Allow-list działa per tenant.
- Brak origin jest obsłużony przewidywalnie.

## Test Plan

- Feature tests CORS headers.
