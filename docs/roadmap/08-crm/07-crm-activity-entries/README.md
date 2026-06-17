# Task: CRM Activity Entries

## Cel

Zapewnić historię zmian dla głównych obiektów CRM.

## Zakres

- Create/update/delete.
- Status changed.
- Assignment/status changed dla tasków.
- Sensitive changed fields.

## Poza Zakresem

- Global security audit events.

## Zależności

- Activity entries.
- CRM models.

## Kroki

- Activity logger calls są w Actions dla contacts, companies, deals, notes i tasks.
- Before/after są zapisywane dla update oraz bezpiecznych pól.
- Sensitive values są maskowane przez centralny redaktor payloadów oraz flagę sensitive note.

## Subtaski

Brak.

## Acceptance Criteria

- Historia odpowiada kto/co/kiedy/na czym.
- Sensitive values nie są plaintextem.
- Testy obejmują główne Actions.

## Test Plan

- Activity tests dla CRM create/update/delete oraz redakcji sensitive payloadów.
