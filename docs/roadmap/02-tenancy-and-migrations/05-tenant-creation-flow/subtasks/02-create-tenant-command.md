# Subtask: Create Tenant Command

## Zadanie

Dodać komendę tworzącą tenanta dla developmentu i operacji.

## Oczekiwane Zmiany

- `tenants:create {slug}` tworzy tenant row, schema, migracje i defaults.
- Komenda używa Action/Service, nie trzyma całej logiki w `handle`.
- Błędy są czytelne dla operatora.

## Obszary

- `app/Console/Commands`
- `app/Modules/Tenancy`
- `database/seeders`

## Checklist

- [x] Komenda działa w local/testing.
- [x] Tenant schema jest utworzona.
- [x] Owner/default settings są gotowe albo jawnie pominięte z TODO.
