# Task: License Management

## Cel

Pokazać i zarządzać stanem licencji tenantów/self-hosted installations.

## Zakres

- License detail view.
- Status valid/expired/grace/perpetual.
- Manual refresh/recheck.

## Poza Zakresem

- Zewnętrzny license server.

## Zależności

- License verification.
- Admin navigation.

## Kroki

- Dodać widok license state.
- Dodać akcję verify.
- Audytować wynik i błędy.

## Subtaski

Brak.

## Acceptance Criteria

- Superadmin widzi effective license state.
- Verify nie loguje sekretów.
- Zmiana state wpływa przez Entitlements.

## Test Plan

- Feature tests license views.
- Unit tests state mapping.
