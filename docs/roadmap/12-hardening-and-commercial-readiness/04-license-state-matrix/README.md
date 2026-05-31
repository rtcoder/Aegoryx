# Task: License State Matrix

## Cel

Przetestować i udokumentować zachowanie produktu dla stanów licencji i subskrypcji.

## Zakres

- Active.
- Trial.
- Grace.
- Expired.
- Suspended.
- Perpetual.

## Poza Zakresem

- Cennik i marketing planów.

## Zależności

- Entitlements.
- Billing/licensing.

## Kroki

- Spisać macierz stanów.
- Dodać tests dla effective entitlements.
- Sprawdzić UI states.

## Subtaski

Brak.

## Acceptance Criteria

- Każdy state ma oczekiwane feature access.
- Expired/suspended nie kasuje danych.
- Perpetual działa bez SaaS subscription.

## Test Plan

- Unit tests entitlement matrix.
- Feature tests gated routes.
