# Task: Support Impersonation Audit

## Cel

Zbudować bezpieczny support access dla superadmina z powodem, expiration i pełnym audytem.

## Zakres

- Support session model.
- Reason required.
- Expiration.
- Banner UI requirement.
- Audit entries dla działań.

## Poza Zakresem

- Ukryte backdoory.
- Support access bez 2FA.

## Zależności

- Admin Console.
- 2FA.
- Audit log.

## Kroki

- Zaprojektować support session lifecycle.
- Wymusić reason i 2FA.
- Zapisywać actor jako superadmin/support.
- Dokumentować zasady w `docs/security/two-factor-and-support-access.md`.

## Subtaski

Brak.

## Acceptance Criteria

- [x] Każde wejście support jest audytowane.
- [x] Sesja support wygasa.
- [x] UI wyraźnie pokazuje tryb support.

## Test Plan

- [x] Feature tests dla start/end support session.
- [x] Audit tests dla akcji w trybie support.
- [x] Test wymogu 2FA dla support mode.
