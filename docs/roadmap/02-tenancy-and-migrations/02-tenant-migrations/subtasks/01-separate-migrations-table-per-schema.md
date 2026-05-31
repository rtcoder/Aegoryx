# Subtask: Separate Migrations Table Per Schema

## Zadanie

Zapewnić, że każda tenant schema ma własną tabelę `migrations`.

## Oczekiwane Zmiany

- Przed migracją ustawiany jest `search_path` na tenant schema.
- Laravel tworzy `tenant_x.migrations`, nie `public.migrations`.
- Po migracji context jest resetowany.

## Obszary

- `app/Console/Commands/MigrateTenantsCommand.php`
- `app/Services/Tenancy`
- `tests/Feature`

## Checklist

- [ ] Test dla `tenant_1.migrations`.
- [ ] Test dla `tenant_2.migrations`.
- [ ] Test potwierdza brak tenant tables w `public`.
