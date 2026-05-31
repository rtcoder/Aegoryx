# Task: Deals

## Cel

Zbudować deale CRM z lifecycle statusami.

## Zakres

- `crm_deals`.
- Status/pipeline fields.
- Relacje z firmą/kontaktem.
- Activity history.

## Poza Zakresem

- Zaawansowany pipeline board.

## Zależności

- Contacts.
- Companies.

## Kroki

- Dodać model i migrację.
- Zdefiniować status enum.
- Rejestrować status changes.

## Subtaski

Brak.

## Acceptance Criteria

- Status biznesowy nie jest zastąpiony soft delete.
- Zmiana statusu ma activity entry.
- Authorization działa dla create/update/delete.

## Test Plan

- Status transition tests.
- Activity tests.
