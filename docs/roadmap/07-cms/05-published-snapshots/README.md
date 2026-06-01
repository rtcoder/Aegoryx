# Task: Published Snapshots

## Status

Done.

## Cel

Przechowywać opublikowaną wersję strony w formie bezpiecznej dla public API.

## Zakres

- `published_pages`.
- Slug/path lookup.
- Cache invalidation signal.

## Poza Zakresem

- CDN integration.

## Zależności

- Publish flow.
- Public API.

## Kroki

- Dodać migration i model.
- Publikacja tworzy/aktualizuje snapshot.
- Odpublikowanie usuwa lub oznacza snapshot jako niedostępny.

## Subtaski

Brak.

## Acceptance Criteria

- Public API nie czyta draft tables.
- Snapshot nie zawiera panel-only fields.
- Odpublikowana strona nie jest publicznie widoczna.

## Test Plan

- Publish/unpublish tests.
- Public visibility tests.
