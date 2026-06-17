# Task: Privacy Audit

## Cel

Sprawdzić, czy dane wrażliwe są szyfrowane, maskowane i nie trafiają do logów.

## Zakres

- CRM sensitive fields.
- Notes.
- Tokens/secrets.
- Audit/activity payloads.

## Poza Zakresem

- Formalny zewnętrzny audyt prawny.

## Zależności

- Security module.
- CRM.
- Audit/activity.

## Kroki

- Zidentyfikować sensitive fields w `docs/security/privacy-audit.md`.
- Sprawdzić storage/logging/activity.
- Dodać redaction tests.

## Subtaski

Brak.

## Acceptance Criteria

- [x] Brak plaintext sekretów w logach.
- [x] Sensitive activity payload jest maskowany.
- [x] Public API nie zwraca private fields.

## Test Plan

- [x] Redaction tests.
- [x] Public API payload assertions.
