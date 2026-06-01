# Subtask: Create System Tables

## Zadanie

Dodać bazowe tabele systemowe w public schema.

## Oczekiwane Zmiany

- `tenants`, `tenant_domains`, `features`, `plans`, `subscriptions`, `licenses`, `identities`.
- Soft deletes tam, gdzie operator może usuwać rekord.
- Actor fields tam, gdzie zmiana jest biznesowo istotna.

## Obszary

- `database/migrations/landlord`
- `app/Models/Landlord`

## Checklist

- [x] Tabele nie zawierają danych biznesowych tenantów.
- [x] Brak FK z tenant schema do public schema.
- [x] Testy migracji przechodzą.
