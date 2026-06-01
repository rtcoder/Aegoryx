# Task: Entitlement Aware UI

## Status

Done.

## Cel

Dostosować panel do dostępnych features i limitów tenanta.

## Zakres

- Shared entitlement props.
- Feature-gated navigation/actions.
- Limit display dla wybranych funkcji.

## Poza Zakresem

- Billing checkout UI.

## Zależności

- Entitlements API.
- Tenant navigation.

## Kroki

- Przekazać effective entitlements do frontendu.
- Użyć ich w panel shell i module navigation.
- Backend nadal weryfikuje dostęp.

## Subtaski

Brak.

## Acceptance Criteria

- UI odzwierciedla plan/licencję.
- Bezpośredni request do zablokowanej funkcji zwraca 403.
- Entitlement props nie zawierają sekretów.

## Test Plan

- Feature tests gated routes.
- Frontend smoke test.
