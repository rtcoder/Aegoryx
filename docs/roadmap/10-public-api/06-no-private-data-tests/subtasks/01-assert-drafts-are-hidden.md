# Subtask: Assert Drafts Are Hidden

## Zadanie

Potwierdzić testem, że draft CMS page nie jest widoczny w public API.

## Oczekiwane Zmiany

- Fixture draft page.
- Request do public endpointu.
- Oczekiwany 404 albo brak rekordu.

## Obszary

- `tests/Feature/PublicApi`
- `app/Modules/PublicApi`

## Checklist

- [x] Draft nie jest zwracany.
- [x] Published snapshot jest zwracany.
- [x] Test działa per tenant.
