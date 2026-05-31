# Task: Test Baseline

## Cel

Utworzyć minimalny zestaw testów chroniący bootstrap aplikacji, moduły i podstawowe route health.

## Zakres

- Testy architektury modułów.
- Test root route i health route.
- Konwencja dla przyszłych testów tenantowych.

## Poza Zakresem

- Pełny test suite tenant isolation.
- Testy UI browserowe.

## Zależności

- Modular bootstrap.

## Kroki

- Dodać test rejestracji modułów.
- Zweryfikować command discovery.
- Opisać gdzie trafiają testy modułów.

## Subtaski

Brak.

## Acceptance Criteria

- `composer test` przechodzi na świeżym checkout.
- Testy łapią brak providerów modułów.
- Testy nie wymagają produkcyjnej bazy.

## Test Plan

- `composer test`
