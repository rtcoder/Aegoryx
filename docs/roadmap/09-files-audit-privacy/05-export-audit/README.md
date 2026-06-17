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

- Dodać `CreateActivityExportAction`.
- Wymagać policy `ActivityEntryPolicy::export`.
- Zapisywać eksport jako prywatny `TenantFile` z `expires_at`.
- Rejestrować `ActivityEntryAction::ActivityExportCreated`.
- Dla dużych eksportów docelowo przenieść akcję do joba z tenant context.

## Subtaski

Brak.

## Acceptance Criteria

- [x] Eksport prywatnych danych wymaga uprawnień.
- [x] Eksport jest audytowany.
- [x] Plik eksportu ma expiration.

## Test Plan

- [x] Feature test eksportu przez panel.
- [x] Audit test dla `activity_export_created`.
