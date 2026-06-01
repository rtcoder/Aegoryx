# Task: Page Model

## Status

Done.

## Cel

Zbudować tenantowy model stron CMS jako bazę dla draftów, rewizji i publikacji.

## Zakres

- `cms_pages`.
- Status lifecycle.
- Soft deletes.
- Actor fields.

## Poza Zakresem

- Public API endpoints.
- Page builder UI.

## Zależności

- Tenant migrations.
- Tenant Panel.

## Kroki

- Dodać tenant migration.
- Dodać model i policy.
- Dodać FormRequests i Resource.

## Subtaski

Brak.

## Acceptance Criteria

- Strona należy do tenant schema.
- Brak `tenant_id` w tabeli.
- Delete jest soft delete.

## Test Plan

- Migration test.
- CRUD authorization tests.
