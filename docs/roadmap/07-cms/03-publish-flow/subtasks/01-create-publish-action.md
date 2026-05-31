# Subtask: Create Publish Action

## Zadanie

Zaimplementować `PublishPageAction` jako jedyne miejsce publikacji CMS page.

## Oczekiwane Zmiany

- Action sprawdza uprawnienia i entitlement.
- Action wykonuje zapis snapshotu transakcyjnie.
- Efekty uboczne są dispatchowane po commicie, jeśli będą potrzebne.

## Obszary

- `app/Modules/Cms/Actions`
- `app/Modules/Cms/Models`

## Checklist

- [ ] Controller jest cienki.
- [ ] Publish działa tylko dla poprawnego tenant context.
- [ ] Test success i unauthorized.
