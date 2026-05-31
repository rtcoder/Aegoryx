# Subtask: Define Preflight Checks

## Zadanie

Opisać checks wymagane przed migracją produkcyjną.

## Oczekiwane Zmiany

- Backup confirmed.
- Maintenance window.
- Pending migrations review.
- Tenant count and batching note.

## Obszary

- `docs/roadmap/11-operations-deployment`
- przyszła dokumentacja deployu

## Checklist

- [ ] Backup jest obowiązkowy.
- [ ] Rollback strategy preferuje forward-only fix.
- [ ] Operator wie, co zrobić przy failed tenant migration.
