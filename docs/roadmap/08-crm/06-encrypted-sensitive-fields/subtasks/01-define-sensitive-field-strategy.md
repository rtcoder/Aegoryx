# Subtask: Define Sensitive Field Strategy

## Zadanie

Ustalić strategię dla pól sensitive w CRM.

## Oczekiwane Zmiany

- Jasna decyzja: encrypted value, lookup hash, masked display.
- Brak plaintext dla email/phone w jawnych kolumnach.
- Notatki oznaczone sensitive są traktowane ostrożniej.

## Obszary

- `app/Modules/Crm`
- `app/Modules/Audit`
- `app/Models/Tenant`

## Checklist

- [x] Strategia opisana.
- [x] Hash lookup nie pozwala odtworzyć wartości.
- [x] Masking działa w activity payload.
