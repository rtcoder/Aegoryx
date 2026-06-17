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

- Done: Contacts, Companies, Deals, Notes, Tasks, Encrypted Sensitive Fields, CRM Activity Entries.

## Taski

- [x] [Contacts](01-contacts/)
- [x] [Companies](02-companies/)
- [x] [Deals](03-deals/)
- [x] [Notes](04-notes/)
- [x] [Tasks](05-tasks/)
- [x] [Encrypted Sensitive Fields](06-encrypted-sensitive-fields/)
- [x] [CRM Activity Entries](07-crm-activity-entries/)

## Definicja Ukończenia

- Główne obiekty CRM mają CRUD, soft deletes i historię.
- Dane wrażliwe są szyfrowane albo maskowane.
- Tenant A nie widzi danych tenant B.
