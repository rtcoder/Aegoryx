# Task: CRM Activity Entries

## Cel

Zapewnić historię zmian dla głównych obiektów CRM.

## Zakres

- Create/update/delete/restore.
- Status changed.
- Owner changed.
- Sensitive changed fields.

## Poza Zakresem

- Global security audit events.

## Zależności

- Activity entries.
- CRM models.

## Kroki

- Dodać activity logger calls w Actions.
- Zapisywać before/after dla bezpiecznych pól.
- Maskować dane sensitive.

## Subtaski

Brak.

## Acceptance Criteria

- Historia odpowiada kto/co/kiedy/na czym.
- Sensitive values nie są plaintextem.
- Testy obejmują główne Actions.

## Test Plan

- Activity tests dla CRM create/update/delete/restore.
