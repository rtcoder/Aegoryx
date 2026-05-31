# Epic 04: Entitlements Billing Licensing

## Cel

Zbudować jedną warstwę decyzyjną dla feature access, limitów, SaaS billing i self-hosted licensing.

## Dlaczego Jest Ważny

Moduły biznesowe nie mogą znać Paddle, planów ani payloadów licencji. Mają pytać tylko Entitlements.

## Zależności

- Tenant model.
- Audit/activity foundation.
- Podstawowy Admin Console.

## Taski

- [Entitlement Keys](01-entitlement-keys/)
- [Plan Limits](02-plan-limits/)
- [Subscription Mapping](03-subscription-mapping/)
- [License Verification](04-license-verification/)
- [Manual Overrides](05-manual-overrides/)

## Definicja Ukończenia

- Moduły używają `allows`, `limit` i `config`.
- Billing i licensing mapują się na wewnętrzne entitlementy.
- Override jest audytowany i ma priorytet jasno opisany.
