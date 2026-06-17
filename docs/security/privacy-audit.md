# Privacy Audit

## Sensitive Data

Za wrażliwe uznajemy:

- hasła i recovery material
- tokeny, sekrety, API keys
- e-mail i telefon w CRM
- wrażliwe notatki CRM
- license payload i provider payload, jeśli zawierają sekrety

## Storage

- Hasła są hashowane przez Laravel cast `hashed`.
- CRM e-mail i telefon są przechowywane jako encrypted value plus hash lookup.
- Private files są metadanymi tenantowymi i dostęp wymaga policy.

## Activity And Audit

`RedactsActivityPayload` maskuje pola wrażliwe, także zagnieżdżone oraz suffixy typu `payment_token`.

Audit log nie może zawierać plaintext sekretów. Dla license verification logujemy status, tenant i typ licencji, bez payload secret.

## Public API

Public API zwraca tylko published CMS snapshots. Nie eksponuje draftów, CRM, users, files ani pól prywatnych.

## Brak Blockerów

Na obecnym etapie brak znanych blockerów prywatności. Przed produkcją wymagana jest powtórka testów redaction i przegląd nowych payloadów integracyjnych.
