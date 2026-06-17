# Task: File Metadata

## Status

Done.

## Implemented Notes

- Panel plików pozwala wysłać prywatny plik.
- Upload zapisuje plik w storage i rejestruje metadane przez `RegisterFileMetadataAction`.
- Upload tworzy activity entry `file_registered`.

## Cel

Zbudować tenantowy model metadanych plików niezależny od fizycznego storage.

## Zakres

- `files`.
- Disk/path/mime/size.
- Owner/visibility.
- Soft deletes.

## Poza Zakresem

- Virus scanning.

## Zależności

- Tenant migrations.
- Storage config.

## Kroki

- Dodać migration/model.
- Oddzielić metadata od binary storage.
- Dodać activity dla register/delete.

## Subtaski

Brak.

## Acceptance Criteria

- Plik jest tenant data.
- Local disk działa dev-only.
- Metadata delete jest soft delete.

## Test Plan

- Migration/model tests.
- Storage fake tests.
- Soft delete activity tests.
