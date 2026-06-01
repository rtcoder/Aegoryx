# Task: CMS Activity History

## Status

Done.

## Cel

Rejestrować historię istotnych akcji CMS.

## Zakres

- Created/updated/deleted/restored.
- Published/unpublished.
- Changed fields bez sekretów.

## Poza Zakresem

- Global security audit.

## Zależności

- Activity entries.
- Page model.

## Kroki

- Dodać wywołania activity loggera w Actions.
- Maskować sensitive values.
- Testować historię per page.

## Subtaski

Brak.

## Acceptance Criteria

- Każda istotna akcja ma actor.
- Historia jest widoczna per element.
- Nie loguje pełnych danych wrażliwych.

## Test Plan

- Activity tests dla create/update/delete/publish.
