# Task: Backup Notes

## Cel

Opisać minimalną strategię backup/restore dla PostgreSQL schema-per-tenant i storage.

## Zakres

- Full database backup.
- Tenant-specific restore considerations.
- Storage backup.

## Poza Zakresem

- Automatyzacja backupów u konkretnego providera.

## Zależności

- Tenancy model.
- Files module.

## Kroki

- Opisać backup całej bazy w `docs/operations/backup-restore.md`.
- Opisać konsekwencje schema-per-tenant.
- Wskazać restore rehearsal jako wymóg.

## Subtaski

Brak.

## Acceptance Criteria

- [x] Restore tenant data nie zakłada cross-schema FK.
- [x] Backup obejmuje storage i DB.
- [x] Dokument wskazuje test restore.

## Test Plan

- [x] Review ops docs.
