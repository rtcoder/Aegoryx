# Epic 02: Tenancy And Migrations

## Cel

Zbudować schema-per-tenant multi-tenancy dla PostgreSQL z osobnymi migracjami landlord i tenant.

## Dlaczego Jest Ważny

Izolacja tenantów jest rdzeniem Aegoryx. Błędy w `search_path`, migracjach albo tabeli `migrations` mogą prowadzić do wycieku danych lub niemożliwych deployów.

## Zależności

- Platform foundation.
- Lokalna baza PostgreSQL.
- Model `Tenant` w public schema.

## Taski

- [Landlord Migrations](01-landlord-migrations/)
- [Tenant Migrations](02-tenant-migrations/)
- [Tenancy Manager](03-tenancy-manager/)
- [Schema Manager](04-schema-manager/)
- [Tenant Creation Flow](05-tenant-creation-flow/)
- [Migration Tests](06-migration-tests/)

## Definicja Ukończenia

- Landlord i tenant migrations są jawnie rozdzielone.
- Każda tenant schema ma własną tabelę `migrations`.
- Tenant context jest ustawiany centralnie i resetowany po pracy.
- Testy potwierdzają izolację minimum dwóch tenantów.
