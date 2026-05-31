# Task: Tasks

## Cel

Zbudować zadania CRM dla follow-upów i pracy operacyjnej.

## Zakres

- `crm_tasks`.
- Due date/status.
- Assignment.
- Activity history.

## Poza Zakresem

- Kalendarz i przypomnienia cykliczne.

## Zależności

- Tenant users.
- Contacts/companies/deals.

## Kroki

- Dodać migration/model.
- Dodać actions status change/assignment.
- Testować ownership i permissions.

## Subtaski

Brak.

## Acceptance Criteria

- Zadanie ma lifecycle status.
- Assignment jest tenant-local.
- Zmiany statusu i właściciela są w historii.

## Test Plan

- CRUD/status tests.
- Authorization tests.
