# Task: Policies

## Cel

Wprowadzić backend authorization dla modyfikujących akcji w modułach.

## Zakres

- Policy pattern dla modułów.
- Gate checks dla akcji niestandardowych.
- Testy braku uprawnień.

## Poza Zakresem

- Pełny system ról i permissions.
- Ukrywanie UI jako jedyny mechanizm bezpieczeństwa.

## Zależności

- Tenant user auth.
- Entitlements dla feature gated actions.

## Kroki

- Zdefiniować minimalne role/prawa.
- Dodać policies do pierwszych modułów.
- Testować 403 dla braku uprawnień.

## Subtaski

Brak.

## Acceptance Criteria

- Każda akcja zapisu ma authorization check.
- Testy potwierdzają brak dostępu.
- Policies nie czytają bezpośrednio billing providerów.

## Test Plan

- Feature tests dla create/update/delete.
