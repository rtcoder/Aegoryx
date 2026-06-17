# Roadmapa Aegoryx

Roadmapa opisuje pełny produkt Aegoryx: SaaS i self-hosted platformę CMS + CRM opartą o Laravel, PostgreSQL schema-per-tenant, publiczne read-only API, audyt, prywatność, billing i licencjonowanie.

To jest plan produktowo-techniczny, nie tracker statusów. Każdy epic ma własny katalog, każdy task ma własny katalog z `README.md`, a złożone taski mają osobne pliki subtasków.

## Kolejność Realizacji

1. [Platform Foundation](01-platform-foundation/)
2. [Tenancy And Migrations](02-tenancy-and-migrations/)
3. [Identity Auth Security](03-identity-auth-security/)
4. [Entitlements Billing Licensing](04-entitlements-billing-licensing/)
5. [Tenant Panel](06-tenant-panel/)
6. [Admin Console](05-admin-console/)
7. [Audit, Files And Privacy](09-files-audit-privacy/)
8. [CMS](07-cms/)
9. [CRM](08-crm/)
10. [Public API](10-public-api/)
11. [Operations Deployment](11-operations-deployment/)
12. [Hardening And Commercial Readiness](12-hardening-and-commercial-readiness/)

## Epici

| Epic | Cel |
| --- | --- |
| [01 Platform Foundation](01-platform-foundation/) | Ustabilizować fundament aplikacji, moduły, standardy i test baseline. |
| [02 Tenancy And Migrations](02-tenancy-and-migrations/) | Zbudować bezpieczny model schema-per-tenant i jawne migracje landlord/tenant. |
| [03 Identity Auth Security](03-identity-auth-security/) | Przygotować identity, tenant users, auth, 2FA, policies i support access. |
| [04 Entitlements Billing Licensing](04-entitlements-billing-licensing/) | Oddzielić decyzje feature access od billing/licensing providerów. |
| [05 Admin Console](05-admin-console/) | Zbudować superadmin console dla tenantów, dostępu do funkcji, licencji i support mode. |
| [06 Tenant Panel](06-tenant-panel/) | Zbudować panel klienta przez Laravel + Livewire. |
| [07 CMS](07-cms/) | Dostarczyć CMS z draftami, rewizjami, publikacją i snapshotami. |
| [08 CRM](08-crm/) | Dostarczyć CRM z kontaktami, firmami, dealami, notatkami i historią zmian. |
| [09 Files Audit Privacy](09-files-audit-privacy/) | Uporządkować pliki, audyt, activity history i prywatność danych. |
| [10 Public API](10-public-api/) | Udostępnić read-only API dla opublikowanych treści CMS. |
| [11 Operations Deployment](11-operations-deployment/) | Przygotować deploy, kolejki, Horizon, backupy i self-hosted operations. |
| [12 Hardening And Commercial Readiness](12-hardening-and-commercial-readiness/) | Domknąć testy izolacji, security, privacy, licensing i launch checklist. |

## Konwencja Plików

- Epic: `docs/roadmap/{nn-epic}/README.md`
- Task: `docs/roadmap/{nn-epic}/{nn-task}/README.md`
- Subtask: `docs/roadmap/{nn-epic}/{nn-task}/subtasks/{nn-subtask}.md`

Task jest gotowy do implementacji dopiero wtedy, gdy ma kryteria akceptacji i test plan.

## Kolejne Iteracje

- [Backlog V2](backlog-v2.md)
