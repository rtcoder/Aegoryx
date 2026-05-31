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

- Spisać wszystkie write endpoints/actions.
- Potwierdzić policy/gate/middleware.
- Dodać brakujące tests.

## Subtaski

Brak.

## Acceptance Criteria

- Każda akcja zapisu ma backend check.
- Testy 403 istnieją dla krytycznych akcji.
- UI-only security nie występuje.

## Test Plan

- Feature tests unauthorized cases.
