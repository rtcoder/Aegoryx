# Task: Encrypted Sensitive Fields

## Cel

Chronić prywatne dane CRM przez szyfrowanie, maskowanie i hashe lookupowe.

## Zakres

- Email/phone encrypted fields.
- Hash fields dla wyszukiwania.
- Masked display.
- Log redaction.

## Poza Zakresem

- Per-tenant encryption keys v1, chyba że zostanie zaplanowane osobno.

## Zależności

- Security module.
- Contacts/notes.

## Kroki

- Strategia encrypt/hash/mask jest wdrożona dla email/phone kontaktów.
- Hash lookup działa przez normalizację i deterministyczny SHA-256.
- Activity payload redaguje email, phone oraz treść sensitive notes.

## Subtaski

- [Define Sensitive Field Strategy](subtasks/01-define-sensitive-field-strategy.md)
- [Add Redaction Tests](subtasks/02-add-redaction-tests.md)

## Acceptance Criteria

- Plaintext email/phone nie są zapisywane w jawnych kolumnach.
- Lookup używa hash, nie decrypt-all.
- Activity log nie ujawnia wartości.

## Test Plan

- Unit/feature tests encrypt/hash.
- Activity redaction tests.
