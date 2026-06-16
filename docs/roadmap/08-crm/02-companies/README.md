# Task: Companies

## Status

Done.

## Cel

Zbudować firmy CRM i relacje z kontaktami.

## Zakres

- `crm_companies`.
- Relacja contact-company.
- Soft deletes.
- Activity history.

## Poza Zakresem

- Zaawansowane enrichment integrations.

## Zależności

- Contacts.
- Activity entries.

## Kroki

- Dodać migration/model.
- Dodać actions i requests.
- Testować relacje w tenant schema.

## Subtaski

Brak.

## Acceptance Criteria

- Firma jest tenant data.
- Usunięcie firmy nie niszczy historii kontaktu.
- Activity pokazuje zmiany relacji.

## Test Plan

- CRUD tests.
- Relation tests.
