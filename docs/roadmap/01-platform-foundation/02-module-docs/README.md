# Task: Module Docs

## Cel

Każdy moduł ma krótki README opisujący odpowiedzialność, granice i najważniejsze reguły.

## Zakres

- README w `app/Modules`.
- README w każdym katalogu modułu.
- Link do zasad modular monolith.

## Poza Zakresem

- Pełna dokumentacja API modułów.
- Diagramy architektury.

## Zależności

- Lista modułów w `config/aegoryx.php`.

## Kroki

- Przejść po katalogach modułów.
- Uzupełnić odpowiedzialność modułu.
- Wskazać granice bezpieczeństwa, tenancy albo entitlements tam, gdzie dotyczy.

## Subtaski

Brak.

## Acceptance Criteria

- Każdy moduł ma README.
- README nie dublują pełnej roadmapy.
- Dokumenty jasno mówią, czego moduł nie powinien robić.

## Test Plan

- `rg --files app/Modules -g README.md`
- Ręczna weryfikacja kompletności względem `config/aegoryx.php`.
