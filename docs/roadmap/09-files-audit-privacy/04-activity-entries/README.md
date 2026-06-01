# Task: Activity Entries

## Status

Done.

## Cel

Zbudować reusable activity history per element.

## Zakres

- `activity_entries`.
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

## Test Plan

- Unit tests logger.
- Feature tests z Actions.
