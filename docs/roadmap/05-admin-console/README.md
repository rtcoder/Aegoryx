# Epic 05: Admin Console

## Cel

Zbudować panel superadmina do zarządzania tenantami, features, licencjami, billingiem i support access.

## Dlaczego Jest Ważny

SaaS i self-hosted wymagają operacyjnego centrum kontroli bez mieszania kontekstu superadmina z tenant userem.

## Zależności

- Identity/auth/security.
- Entitlements.
- Tenant model.

## Taski

- [Admin Navigation](01-admin-navigation/)
- [Tenant Management](02-tenant-management/)
- [Feature Management](03-feature-management/)
- [License Management](04-license-management/)
- [Support Sessions](05-support-sessions/)

## Definicja Ukończenia

- Superadmin ma osobny obszar aplikacji.
- Operacje krytyczne są autoryzowane i audytowane.
- Support access wymaga powodu, ma expiration i widoczny kontekst.
