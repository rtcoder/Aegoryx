# Task: Export Audit

## Cel

Audytować eksporty danych prywatnych i przygotować bezpieczny model większych eksportów jako jobs.

## Zakres

- Export request action.
- Permission check.
- Audit entry.
- Expiring file metadata.

## Poza Zakresem

- Wszystkie formaty eksportu.

## Zależności

- Files.
- Audit log.
- Queue setup.

## Kroki

- Dodać standard dla export actions.
- Zapisywać actor/reason/scope.
- Dla dużych eksportów użyć job z tenant_id.

## Subtaski

Brak.

## Acceptance Criteria

- Eksport prywatnych danych wymaga uprawnień.
- Eksport jest audytowany.
- Plik eksportu ma expiration.

## Test Plan

- Feature tests export allowed/forbidden.
- Audit tests.
