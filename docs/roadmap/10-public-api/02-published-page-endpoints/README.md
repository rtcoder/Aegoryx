# Task: Published Page Endpoints

## Status

Done.

## Cel

Udostępnić endpointy read-only dla opublikowanych stron.

## Zakres

- Page by path.
- Page collection where safe.
- Public Resource.

## Poza Zakresem

- Draft preview.
- CRM endpoints.

## Zależności

- Published snapshots.
- Public tenant resolving.

## Kroki

- Dodać route group public API.
- Czytać tylko `published_pages`.
- Zwracać kontrolowany Resource.

## Subtaski

Brak.

## Acceptance Criteria

- Endpoint nie zwraca draftów.
- Endpoint nie zwraca panel-only fields.
- Metody zapisu nie istnieją.

## Test Plan

- Feature tests GET published/unpublished.
- Assert 405/404 for write methods.
