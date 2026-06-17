# Task: Retention Strategy

## Cel

Opisać i przygotować politykę retention, anonimizacji, eksportu i trwałego usuwania danych.

## Zakres

- Dokument decyzji.
- Kategorie danych.
- Proces hard delete po retention.

## Poza Zakresem

- Pełna automatyzacja GDPR v1.

## Zależności

- Files.
- Audit/activity.
- CRM sensitive fields.

## Kroki

- Sklasyfikować dane w `docs/privacy/retention-strategy.md`.
- Dodać konfigurację `aegoryx.retention`.
- Dodać `RetentionPolicy` jako typowany punkt użycia konfiguracji.
- Zaplanować joby cleanup dla kolejnych etapów.

## Subtaski

Brak.

## Acceptance Criteria

- [x] Hard delete nie jest przypadkowy.
- [x] Dane prywatne mają kierunek anonimizacji/usuwania.
- [x] Audit zachowuje zgodność bez sekretów.

## Test Plan

- [x] Review dokumentu retention.
- [x] Unit test konfiguracji `RetentionPolicy`.
