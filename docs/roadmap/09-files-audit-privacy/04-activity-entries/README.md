# Task: Activity Entries

## Status

Done.

## Cel

Zbudować reusable activity history per element.

## Zakres

- `activity_entries`.
- Tenant activity browser dla uprawnionych użytkowników.
- Subject/actor/action.
- Before/after metadata.
- Tenant context.

## Poza Zakresem

- Full-text activity search.

## Zależności

- Tenant migrations.
- Identity/auth.

## Kroki

- Dodać model i migration.
- Dodać `ActivityLogger`.
- Wymusić redaction dla sensitive fields.

## Subtaski

Brak.

## Acceptance Criteria

- Activity odpowiada kto/kiedy/co/na czym.
- Activity jest per tenant dla tenant data.
- Sensitive before/after jest maskowane.
- Owner/admin może przeglądać activity history, viewer nie ma dostępu.

## Test Plan

- Unit tests logger.
- Feature tests z Actions.
- Feature tests widoku activity i autoryzacji.
