# Task: Two Factor Auth

## Status

Done.

## Implemented Notes

- Landlord identities mają pełny flow 2FA z challenge i ustawieniami security.
- Tenant users mają akcje domenowe enable/disable 2FA z tenantowym activity audit.
- Zmiana cudzego tenant 2FA wymaga roli `owner/admin`; user może zarządzać własnym 2FA.

## Cel

Dodać 2FA dla kont wymagających podwyższonego bezpieczeństwa, szczególnie superadminów.

## Zakres

- TOTP secret storage.
- Recovery codes.
- Wymuszenie 2FA dla support access.
- Audit events.

## Poza Zakresem

- Hardware keys/WebAuthn.

## Zależności

- Auth decision.
- Security audit log.

## Kroki

- Wybrać mechanizm 2FA.
- Szyfrować sekrety i recovery codes.
- Dodać audit dla zmian 2FA.
- Opisać ograniczenia i dalszy UI w `docs/security/two-factor-and-support-access.md`.

## Subtaski

Brak.

## Acceptance Criteria

- [x] Sekrety 2FA nie są logowane plaintextem.
- [x] Recovery codes są zabezpieczone.
- [x] Support mode wymaga 2FA.

## Test Plan

- [x] Test enable/disable 2FA.
- [x] Test audit event.
