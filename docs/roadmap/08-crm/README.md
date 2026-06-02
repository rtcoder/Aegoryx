# Epic 08: CRM

## Cel

Zbudować CRM dla kontaktów, firm, deali, notatek i zadań z historią zmian oraz ochroną danych wrażliwych.

## Dlaczego Jest Ważny

CRM będzie przechowywać prywatne dane klientów końcowych, więc izolacja, szyfrowanie i audyt są wymagane od początku.

## Zależności

- Tenancy.
- Tenant Panel.
- Audit/activity foundation.

## Status

- Done: Contacts.
- Partial: encrypted sensitive fields and CRM activity entries are implemented for contacts.
- Next: Companies, Deals, Notes, Tasks, generalized sensitive field strategy.

## Taski

- [x] [Contacts](01-contacts/)
- [ ] [Companies](02-companies/)
- [ ] [Deals](03-deals/)
- [ ] [Notes](04-notes/)
- [ ] [Tasks](05-tasks/)
- [ ] [Encrypted Sensitive Fields](06-encrypted-sensitive-fields/)
- [ ] [CRM Activity Entries](07-crm-activity-entries/)

## Definicja Ukończenia

- Główne obiekty CRM mają CRUD, soft deletes i historię.
- Dane wrażliwe są szyfrowane albo maskowane.
- Tenant A nie widzi danych tenant B.
