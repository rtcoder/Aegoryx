# Task: Support Sessions

## Cel

Obsłużyć uruchamianie, widoczność i kończenie support sessions.

## Zakres

- Start support session.
- Reason required.
- Expiration.
- End session.
- Audit timeline.

## Poza Zakresem

- Tenant user impersonation bez superadmin 2FA.

## Zależności

- Support impersonation audit.
- 2FA.
- Tenant management.

## Kroki

- Dodać actions start/end.
- Zapisywać support context w sesji.
- Pokazywać banner trybu support.

## Subtaski

Brak.

## Acceptance Criteria

- Bez powodu nie da się wejść w support mode.
- Support session wygasa.
- Wszystkie akcje w trybie support są audytowane.

## Test Plan

- Feature tests start/end/expired.
- Audit tests.
