# Task: File Metadata

## Cel

Zbudować tenantowy model metadanych plików niezależny od fizycznego storage.

## Zakres

- `files`.
- Disk/path/mime/size.
- Owner/visibility.
- Soft deletes.

## Poza Zakresem

- Pełny upload UI.
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
