# Epic 06: Tenant Panel

## Cel

Zbudować panel klienta w Laravel + Livewire, działający w tenant context.

## Dlaczego Jest Ważny

To główna powierzchnia pracy użytkownika tenanta. Musi być ergonomiczna, modularna i zgodna z Entitlements.

## Zależności

- Tenancy.
- Auth.
- Entitlements.

## Status

- Done: Livewire Setup.
- In progress: none.
- Pending: Panel Shell, Tenant Navigation, Authorization UX, Entitlement Aware UI.

## Taski

- [x] [Livewire Setup](01-livewire-setup/)
- [Panel Shell](02-panel-shell/)
- [Tenant Navigation](03-tenant-navigation/)
- [Authorization UX](04-authorization-ux/)
- [Entitlement Aware UI](05-entitlement-aware-ui/)

## Definicja Ukończenia

- Panel działa przez Livewire.
- Routing tenantowy wymaga tenant context.
- UI pokazuje dostęp według Entitlements, a backend nadal wymusza autoryzację.
