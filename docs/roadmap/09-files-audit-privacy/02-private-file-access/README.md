# Task: Private File Access

## Cel

Zapewnić autoryzowany i audytowany dostęp do prywatnych plików.

## Zakres

- Download action.
- Policy checks.
- Signed/temporary response.
- Audit entry.

## Poza Zakresem

- Public asset CDN.

## Zależności

- File metadata.
- Audit log.

## Kroki

- Dodać download controller/action.
- Sprawdzać policy przed storage access.
- Zapisywać audit dla download.

## Subtaski

Brak.

## Acceptance Criteria

- Nieautoryzowany download zwraca 403.
- Każdy private download jest audytowany.
- Response nie ujawnia internal storage path bez potrzeby.

## Test Plan

- Feature tests download allowed/forbidden.
- Audit tests.
