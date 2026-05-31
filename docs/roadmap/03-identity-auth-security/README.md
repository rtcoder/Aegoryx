# Epic 03: Identity Auth Security

## Cel

Przygotować global identity, tenant user authentication, autoryzację, 2FA i bezpieczny support access.

## Dlaczego Jest Ważny

Aegoryx operuje na prywatnych danych CRM/CMS. Dostęp musi być kontrolowany po stronie backendu, audytowany i gotowy pod SaaS oraz self-hosted.

## Zależności

- Tenancy context.
- Podstawowe modele landlord/tenant.
- Decyzja panelowa dla Inertia/Fortify/Jetstream.

## Taski

- [Identity Model](01-identity-model/)
- [Tenant User Auth](02-tenant-user-auth/)
- [Fortify Jetstream Decision](03-fortify-jetstream-decision/)
- [Policies](04-policies/)
- [Two Factor Auth](05-two-factor-auth/)
- [Support Impersonation Audit](06-support-impersonation-audit/)

## Definicja Ukończenia

- Identity i tenant users mają jasny podział odpowiedzialności.
- Modyfikujące akcje mają authorization checks.
- 2FA i support mode mają audyt i ograniczenia bezpieczeństwa.
