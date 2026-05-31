# Subtask: Generate Safe Schema Name

## Zadanie

Generować bezpieczne nazwy schem tenantów.

## Oczekiwane Zmiany

- Preferowany format: `tenant_{id}`.
- Raw slug nie trafia do SQL schema name.
- Identyfikator jest cytowany przez schema manager.

## Obszary

- Tenant creation Action/Service.
- `PostgresSchemaManager`.

## Checklist

- [ ] Test `tenant_1`.
- [ ] Test odrzuca raw unsafe input.
- [ ] Schema rename nie jest zależna od zmiany slug.
