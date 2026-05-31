# Task: Admin Navigation

## Cel

Zbudować osobną nawigację i shell dla superadmina.

## Zakres

- Admin routes.
- Layout Admin Console.
- Widoczne sekcje tenants, features, licenses, billing, support.

## Poza Zakresem

- Tenant Panel routes.
- Support impersonation logic.

## Zależności

- Auth/security.
- Inertia setup.

## Kroki

- Dodać admin route group.
- Przygotować layout.
- Ukrywać sekcje według policies, nie tylko frontend state.

## Subtaski

Brak.

## Acceptance Criteria

- Superadmin i tenant user mają różne powierzchnie UI.
- Admin Console wymaga odpowiedniego guard/policy.
- Nawigacja nie inicjalizuje tenant context bez potrzeby.

## Test Plan

- Feature tests 403/200 dla admin routes.
