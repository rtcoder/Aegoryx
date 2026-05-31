# Task: No Private Data Tests

## Cel

Utworzyć testy gwarantujące, że public API nie zwraca danych prywatnych, draftów ani CRM.

## Zakres

- Draft visibility tests.
- Private fields assertions.
- CRM route absence.

## Poza Zakresem

- Pełny security pentest.

## Zależności

- Published endpoints.
- CMS snapshots.

## Kroki

- Przygotować fixture draft/published.
- Sprawdzić payload public Resource.
- Sprawdzić brak CRM endpoints.

## Subtaski

- [Assert Drafts Are Hidden](subtasks/01-assert-drafts-are-hidden.md)
- [Assert CRM Is Not Exposed](subtasks/02-assert-crm-is-not-exposed.md)

## Acceptance Criteria

- Draft nie pojawia się w public response.
- Private panel fields nie pojawiają się w JSON.
- CRM data nie ma public routes.

## Test Plan

- `php artisan test --filter=PublicApi`
