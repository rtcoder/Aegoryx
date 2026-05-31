# Epic 06: Tenant Panel

## Cel

Zbudować panel klienta w Laravel + Inertia + TypeScript, działający w tenant context.

## Dlaczego Jest Ważny

To główna powierzchnia pracy użytkownika tenanta. Musi być ergonomiczna, modularna i zgodna z Entitlements.

## Zależności

- Tenancy.
- Auth.
- Entitlements.

## Taski

- [Inertia Setup](01-inertia-setup/)
- [Panel Shell](02-panel-shell/)
- [Tenant Navigation](03-tenant-navigation/)
- [Authorization UX](04-authorization-ux/)
- [Entitlement Aware UI](05-entitlement-aware-ui/)

## Definicja Ukończenia

- Panel działa przez Inertia i TypeScript.
- Routing tenantowy wymaga tenant context.
- UI pokazuje dostęp według Entitlements, a backend nadal wymusza autoryzację.
