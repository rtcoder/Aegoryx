# Task: Two Factor Auth

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

## Subtaski

Brak.

## Acceptance Criteria

- Sekrety 2FA nie są logowane plaintextem.
- Recovery codes są zabezpieczone.
- Support mode wymaga 2FA.

## Test Plan

- Test enable/disable 2FA.
- Test audit event.
