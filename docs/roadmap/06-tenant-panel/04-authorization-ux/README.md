# Task: Authorization UX

## Cel

Pokazać użytkownikowi jasne stany braku uprawnień bez osłabiania backend security.

## Zakres

- 403 page.
- Disabled actions.
- Permission-aware controls.

## Poza Zakresem

- System ról enterprise.

## Zależności

- Policies.
- Panel shell.

## Kroki

- Dodać UI dla 403.
- Ustalić standard disabled action.
- Testować backend 403.

## Subtaski

Brak.

## Acceptance Criteria

- UI nie jest jedyną warstwą ochrony.
- Brak uprawnień ma czytelny komunikat.
- Actions ukryte/disabled mają backend test.

## Test Plan

- Feature tests 403.
- Browser smoke dla 403 page.
