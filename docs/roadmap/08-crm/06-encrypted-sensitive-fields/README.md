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

- Zdefiniować strategię encrypt/hash/mask.
- Dodać service lub cast.
- Testować brak plaintext w audit/logs.

## Subtaski

- [Define Sensitive Field Strategy](subtasks/01-define-sensitive-field-strategy.md)
- [Add Redaction Tests](subtasks/02-add-redaction-tests.md)

## Acceptance Criteria

- Plaintext email/phone nie są zapisywane w jawnych kolumnach.
- Lookup używa hash, nie decrypt-all.
- Activity log nie ujawnia wartości.

## Test Plan

- Unit tests encrypt/hash.
- Activity redaction tests.
