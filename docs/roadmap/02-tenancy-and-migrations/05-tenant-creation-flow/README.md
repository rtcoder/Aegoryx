# Task: Tenant Creation Flow

## Status

Done.

## Cel

Zbudować spójny proces tworzenia tenanta: wpis landlord, schema, migracje, seedery i owner membership.

## Zakres

- Action lub Service tworzenia tenanta.
- Bezpieczne generowanie `schema_name`.
- Komenda `tenants:create`.
- Seed default settings i opcjonalnych tenant feature overrides.

## Poza Zakresem

- UI Admin Console.
- Billing checkout.

## Zależności

- Landlord migrations.
- Tenant migrations.
- Schema manager.

## Kroki

- Utworzyć tenant row w transakcji tam, gdzie możliwe.
- Wygenerować `tenant_{id}` jako preferowany schema name.
- Utworzyć schema i uruchomić tenant migrations.
- Utworzyć właściciela i defaults.

## Subtaski

- [Generate Safe Schema Name](subtasks/01-generate-safe-schema-name.md)
- [Create Tenant Command](subtasks/02-create-tenant-command.md)

## Acceptance Criteria

- Tenant powstaje z działającą tenant schema.
- Raw slug nie jest używany jako schema name.
- Nieudana migracja zostawia jasny błąd operacyjny.

## Test Plan

- Test tworzenia dwóch tenantów.
- Test schema names `tenant_1`, `tenant_2`.
- Test rollback/cleanup dla nieudanej ścieżki w testing.
