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

- [x] Test `tenant_1`.
- [x] Test odrzuca raw unsafe input.
- [x] Schema rename nie jest zależna od zmiany slug.
