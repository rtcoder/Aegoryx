# Task: Authorization Audit

## Cel

Przejrzeć wszystkie akcje modyfikujące pod kątem authorization checks.

## Zakres

- CMS actions.
- CRM actions.
- Admin actions.
- File downloads/exports.

## Poza Zakresem

- Zmiana modelu ról, jeśli nie wynika z luk.

## Zależności

- Policies.
- Główne moduły produktowe.

## Kroki

- Spisać wszystkie write endpoints/actions w `docs/security/authorization-audit.md`.
- Potwierdzić policy/gate/middleware.
- Dodać brakujące tests.

## Subtaski

Brak.

## Acceptance Criteria

- [x] Każda akcja zapisu ma backend check.
- [x] Testy 403 istnieją dla krytycznych akcji.
- [x] UI-only security nie występuje.

## Test Plan

- [x] Feature tests unauthorized cases.
- [x] Audit docs review.
