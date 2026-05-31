# Task: Page Revisions

## Cel

Zapisywać historię edycji stron CMS jako rewizje.

## Zakres

- `cms_page_revisions`.
- Snapshot draft content.
- Actor who edited.

## Poza Zakresem

- Wizualny diff revisions.

## Zależności

- Page model.
- Activity entries.

## Kroki

- Dodać migration i model revisions.
- Tworzyć rewizję przy istotnych zmianach.
- Testować actor i content snapshot.

## Subtaski

Brak.

## Acceptance Criteria

- Edycja strony tworzy rewizję.
- Rewizja nie nadpisuje wcześniejszych danych.
- Sensitive/private panel data nie trafia do public snapshot.

## Test Plan

- Feature tests update page creates revision.
