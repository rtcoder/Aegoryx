# Task: Manual Overrides

## Status

Done.

## Implemented Notes

- Manual override ma najwyższy priorytet nad planem i licencją.
- Override może zmienić feature access i limity.
- Zmiany override są audytowane przez `SetTenantFeatureOverrideAction`.

## Cel

Pozwolić superadminowi jawnie nadpisać feature access lub limity tenanta.

## Zakres

- Override model.
- Priority względem plan/license.
- Reason i audit.

## Poza Zakresem

- Samo UI Admin Console, poza wymaganiami.

## Zależności

- Entitlements.
- Admin Console.
- Audit log.

## Kroki

- Dodać model override.
- Określić priorytet.
- Audytować create/update/delete override.

## Subtaski

Brak.

## Acceptance Criteria

- Override ma powód i aktora.
- Entitlements uwzględnia override.
- Usunięcie override przywraca plan/license behavior.

## Test Plan

- Unit tests precedence.
- Audit tests.
