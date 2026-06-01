# Task: Tenant User Auth

## Status

Done.

## Cel

Przygotować logowanie tenant userów w aktywnym tenant context.

## Zakres

- Tenant user model.
- Auth provider wskazujący tenant model.
- Password reset tokens w tenant schema.

## Poza Zakresem

- Admin Console superadmin auth.
- Social login.

## Zależności

- Tenancy middleware/resolver.
- Tenant migrations.

## Kroki

- Upewnić się, że auth query działa po inicjalizacji tenancy.
- Oddzielić tenant user od global identity.
- Dodać test tenant A nie loguje usera tenant B.

## Subtaski

Brak.

## Acceptance Criteria

- Auth nie odpytuje `public.users` dla tenant userów.
- Sesja nie miesza tenantów.
- Password reset działa per tenant.

## Test Plan

- Feature test logowania tenant usera.
- Test negatywny cross-tenant login.
