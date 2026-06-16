# Subtask: Assert No Tenant Tables In Public

## Zadanie

Dodać test wykrywający przypadkowe utworzenie tenant tables w public schema.

## Oczekiwane Zmiany

- Test wykonuje tenant migrations.
- Test sprawdza `to_regclass('public.crm_contacts')` albo inną tenant table.
- Oczekiwany wynik to `null`.

## Obszary

- `tests/Feature`
- `database/migrations/tenant`

## Checklist

- [x] Test failuje, jeśli tenant migration idzie do `public`.
- [x] Test działa dla dwóch tenantów.
