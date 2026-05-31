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

- [ ] Draft nie jest zwracany.
- [ ] Published snapshot jest zwracany.
- [ ] Test działa per tenant.
