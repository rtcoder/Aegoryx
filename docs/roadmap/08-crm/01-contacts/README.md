# Task: Contacts

## Cel

Zbudować podstawowy CRUD kontaktów CRM w tenant schema.

## Zakres

- `crm_contacts`.
- Controller + FormRequest + Action + Resource.
- Soft deletes i actor fields.

## Poza Zakresem

- Import/export kontaktów.
- Zaawansowane custom fields.

## Zależności

- Tenant Panel.
- Encrypted sensitive fields.

## Kroki

- Dodać migration/model/policy.
- Dodać actions create/update/delete/restore.
- Rejestrować activity.

## Subtaski

Brak.

## Acceptance Criteria

- Kontakt nie ma `tenant_id`.
- Email/phone są chronione zgodnie ze strategią sensitive fields.
- Tenant isolation jest testowana.

## Test Plan

- CRUD feature tests.
- Tenant isolation test.
