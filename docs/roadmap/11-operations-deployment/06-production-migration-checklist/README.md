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

- Spisać checks przed migracją.
- Uwzględnić `--continue-on-error` policy.
- Opisać monitoring po migracji.

## Subtaski

- [Define Preflight Checks](subtasks/01-define-preflight-checks.md)

## Acceptance Criteria

- Checklist wymaga backupu.
- Rollback w produkcji jest ostrożny i wyjątkowy.
- Tenant migration failure ma jasną procedurę.

## Test Plan

- Review checklist.
