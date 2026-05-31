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

- Zidentyfikować sensitive fields.
- Sprawdzić storage/logging/activity.
- Dodać redaction tests.

## Subtaski

Brak.

## Acceptance Criteria

- Brak plaintext sekretów w logach.
- Sensitive activity payload jest maskowany.
- Public API nie zwraca private fields.

## Test Plan

- Redaction tests.
- Public API payload assertions.
