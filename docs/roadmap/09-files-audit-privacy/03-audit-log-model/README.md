# Task: Audit Log Model

## Status

Done.

## Cel

Zbudować techniczny audit log dla zdarzeń bezpieczeństwa i operacji krytycznych.

## Zakres

- Audit log schema.
- Landlord audit log browser dla superadmina.
- Actor fields.
- IP/user agent.
- Metadata bez sekretów.

## Poza Zakresem

- Activity history per element.

## Zależności

- Identity/auth/security.

## Kroki

- Dodać landlord/tenant audit strategy.
- Zdefiniować event types.
- Dodać redaction policy.

## Subtaski

Brak.

## Acceptance Criteria

- Audit log nie zapisuje sekretów.
- Security events mają actor i context.
- Log odróżnia system/job/user/superadmin.
- Superadmin może przeglądać audit log w panelu landlorda.

## Test Plan

- Unit tests audit writer.
- Redaction tests.
- Feature test widoku audit log dla superadmina.
