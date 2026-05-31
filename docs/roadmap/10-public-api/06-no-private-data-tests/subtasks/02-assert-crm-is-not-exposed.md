# Subtask: Assert CRM Is Not Exposed

## Zadanie

Potwierdzić, że public API nie ma endpointów CRM ani danych CRM w payloadach.

## Oczekiwane Zmiany

- Test braku public CRM routes.
- Assertion payloadu published page.
- Regression test dla prywatnych pól.

## Obszary

- `routes`
- `app/Modules/PublicApi`
- `tests/Feature/PublicApi`

## Checklist

- [ ] Public API nie ma CRM route group.
- [ ] Payload nie zawiera CRM data.
- [ ] Brak private user fields.
