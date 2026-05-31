# Subtask: Define Sensitive Field Strategy

## Zadanie

Ustalić strategię dla pól sensitive w CRM.

## Oczekiwane Zmiany

- Jasna decyzja: encrypted value, lookup hash, masked display.
- Brak plaintext dla email/phone w jawnych kolumnach.
- Notatki oznaczone sensitive są traktowane ostrożniej.

## Obszary

- `app/Modules/Crm`
- `app/Modules/Security`
- `AGENT_CODING_GUIDELINES.md`

## Checklist

- [ ] Strategia opisana.
- [ ] Hash lookup nie pozwala odtworzyć wartości.
- [ ] Masking działa w Resource.
