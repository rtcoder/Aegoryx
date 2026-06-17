# Task: Production Migration Checklist

## Cel

Przygotować checklistę bezpiecznego uruchamiania migracji produkcyjnych.

## Zakres

- Preflight checks.
- Maintenance mode.
- Backup requirement.
- Tenant batching future note.

## Poza Zakresem

- Automatyczny migrator z UI.

## Zależności

- Landlord/tenant migration commands.

## Kroki

- Spisać checks przed migracją w `docs/operations/production-migrations.md`.
- Uwzględnić `--continue-on-error` policy.
- Opisać monitoring po migracji.

## Subtaski

- [Define Preflight Checks](subtasks/01-define-preflight-checks.md)

## Acceptance Criteria

- [x] Checklist wymaga backupu.
- [x] Rollback w produkcji jest ostrożny i wyjątkowy.
- [x] Tenant migration failure ma jasną procedurę.

## Test Plan

- [x] Review checklist.
