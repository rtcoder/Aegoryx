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

## Subtaski

Brak.

## Acceptance Criteria

- Każde wejście support jest audytowane.
- Sesja support wygasa.
- UI wyraźnie pokazuje tryb support.

## Test Plan

- Feature tests dla start/end support session.
- Audit tests dla akcji w trybie support.
