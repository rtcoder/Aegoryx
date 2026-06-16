# Epic 05: Admin Console

## Cel

Zbudować panel superadmina do zarządzania tenantami, dostępem do funkcji systemowych, licencjami, billingiem i support access.

## Dlaczego Jest Ważny

SaaS i self-hosted wymagają operacyjnego centrum kontroli bez mieszania kontekstu superadmina z tenant userem.

## Zależności

- Identity/auth/security.
- Entitlements.
- Tenant model.

## Status

- Done: Admin Navigation, Tenant Management, Feature Management, License Management, Support Sessions.
- In progress: none.

## Taski

- [x] [Admin Navigation](01-admin-navigation/)
- [x] [Tenant Management](02-tenant-management/)
- [x] [Feature Management](03-feature-management/)
- [x] [License Management](04-license-management/)
- [x] [Support Sessions](05-support-sessions/)

## Definicja Ukończenia

- Superadmin ma osobny obszar aplikacji.
- Operacje krytyczne są autoryzowane i audytowane.
- Support access wymaga powodu, ma expiration i widoczny kontekst.
