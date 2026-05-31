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

- Opisać backup całej bazy.
- Opisać konsekwencje schema-per-tenant.
- Wskazać restore rehearsal jako wymóg.

## Subtaski

Brak.

## Acceptance Criteria

- Restore tenant data nie zakłada cross-schema FK.
- Backup obejmuje storage i DB.
- Dokument wskazuje test restore.

## Test Plan

- Review ops docs.
