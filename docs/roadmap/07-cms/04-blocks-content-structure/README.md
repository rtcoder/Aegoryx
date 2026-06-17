# Task: Blocks Content Structure

## Cel

Ustalić strukturę treści CMS dla bloków i pól strony.

## Zakres

- JSON content schema.
- Walidacja bloków.
- Minimalne typy bloków startowych.

## Poza Zakresem

- Zaawansowany visual builder.

## Zależności

- Page model.
- Page revisions.

## Kroki

- Zdefiniować minimalny format content JSON.
- Dodać walidację i normalizację w warstwie support.
- Zapisać znormalizowany content w revisions i published snapshots.

## Subtaski

Brak.

## Acceptance Criteria

- Nieprawidłowe bloki są odrzucane.
- Format jest wersjonowalny.
- Published snapshot używa zatwierdzonego content.

## Test Plan

- Request validation tests.
- Revision snapshot tests.
