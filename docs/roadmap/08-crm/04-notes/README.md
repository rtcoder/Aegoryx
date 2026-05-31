# Task: Notes

## Cel

Zbudować notatki CRM przypinane do kontaktów, firm lub deali.

## Zakres

- `crm_notes`.
- Polymorphic/logical subject.
- Sensitive flag.
- Soft deletes.

## Poza Zakresem

- Rich text editor.

## Zależności

- Contacts/companies/deals.
- Sensitive fields strategy.

## Kroki

- Dodać migration/model.
- Dodać subject validation.
- Maskować/szyfrować sensitive notes.

## Subtaski

Brak.

## Acceptance Criteria

- Sensitive note nie jest logowana plaintextem.
- Note delete jest soft delete.
- Subject należy do aktywnego tenant context.

## Test Plan

- CRUD tests.
- Sensitive logging test.
