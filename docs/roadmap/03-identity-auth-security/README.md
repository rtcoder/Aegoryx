# Epic 03: Identity Auth Security

## Cel

Przygotować global identity, tenant user authentication, autoryzację, 2FA i bezpieczny support access.

## Dlaczego Jest Ważny

Aegoryx operuje na prywatnych danych CRM/CMS. Dostęp musi być kontrolowany po stronie backendu, audytowany i gotowy pod SaaS oraz self-hosted.

## Zależności

- Tenancy context.
- Podstawowe modele landlord/tenant.
- Decyzja panelowa dla Inertia/Fortify/Jetstream.

## Status

- Done: Identity Model, Tenant User Auth, Fortify Jetstream Decision.
- Next: Policies, Two Factor Auth, Support Impersonation Audit hardening pod 2FA.

## Taski

- [x] [Identity Model](01-identity-model/)
- [x] [Tenant User Auth](02-tenant-user-auth/)
- [x] [Fortify Jetstream Decision](03-fortify-jetstream-decision/)
- [ ] [Policies](04-policies/)
- [ ] [Two Factor Auth](05-two-factor-auth/)
- [ ] [Support Impersonation Audit](06-support-impersonation-audit/)

## Definicja Ukończenia

- Identity i tenant users mają jasny podział odpowiedzialności.
- Modyfikujące akcje mają authorization checks.
- 2FA i support mode mają audyt i ograniczenia bezpieczeństwa.
