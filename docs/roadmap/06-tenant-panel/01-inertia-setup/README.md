# Task: Inertia Setup

## Cel

Skonfigurować Laravel + Inertia + TypeScript jako bazę paneli.

## Zakres

- Inertia adapter.
- TypeScript entrypoint.
- Shared props.
- Podstawowy routing panelowy.

## Poza Zakresem

- Pełny design system.
- CMS/CRM screens.

## Zależności

- Frontend build.
- Auth decision.

## Kroki

- Zainstalować i skonfigurować Inertia.
- Ustawić TS i strukturę `resources/js/Modules`.
- Dodać smoke test build.

## Subtaski

Brak.

## Acceptance Criteria

- `npm run build` przechodzi.
- Inertia renderuje pierwszą stronę panelu.
- Shared props nie wyciekają sekretów.

## Test Plan

- `npm run build`
- Feature test renderowania Inertia response.
