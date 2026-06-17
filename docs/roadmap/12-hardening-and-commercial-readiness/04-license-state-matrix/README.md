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

- Spisać macierz stanów w `docs/product/license-state-matrix.md`.
- Dodać `LicenseStateMatrix`.
- Dodać tests dla stanów blokujących i perpetual.
- Sprawdzić UI states.

## Subtaski

Brak.

## Acceptance Criteria

- [x] Każdy state ma oczekiwane feature access.
- [x] Expired/suspended nie kasuje danych.
- [x] Perpetual działa bez SaaS subscription.

## Test Plan

- [x] Unit tests license matrix.
- [x] Feature tests gated routes.
