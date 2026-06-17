# Epic 04: Entitlements Billing Licensing

## Status

In Progress.

## Cel

Zbudować jedną warstwę decyzyjną dla feature access, limitów, SaaS billing i self-hosted licensing.

## Dlaczego Jest Ważny

Moduły biznesowe nie mogą znać Paddle, planów ani payloadów licencji. Mają pytać tylko Entitlements.

## Zależności

- Tenant model.
- Audit/activity foundation.
- Podstawowy Admin Console.

## Taski

- [x] [Entitlement Keys](01-entitlement-keys/)
- [x] [Plan Limits](02-plan-limits/)
- [ ] [Subscription Mapping](03-subscription-mapping/)
- [x] [License Verification](04-license-verification/)
- [x] [Manual Overrides](05-manual-overrides/)

## Definicja Ukończenia

- Moduły używają `allows`, `limit` i `config`.
- Billing i licensing mapują się na wewnętrzne entitlementy.
- Override jest audytowany i ma priorytet jasno opisany.
