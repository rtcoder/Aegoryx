# Task: Livewire Setup

## Status

Done.

## Cel

Skonfigurować Laravel + Livewire jako bazę paneli tenantowych.

## Zakres

- Livewire.
- Shared props.
- Podstawowy routing panelowy.

## Poza Zakresem

- Pełny design system.
- CMS/CRM screens.

## Zależności

- Frontend build.
- Auth decision.

## Kroki

- Zainstalować i skonfigurować Livewire.
- Ustawić strukturę widoków tenant panelu.
- Dodać smoke test build.

## Subtaski

Brak.

## Acceptance Criteria

- `npm run build` przechodzi.
- Livewire/Blade renderuje pierwszą stronę panelu.
- Shared props nie wyciekają sekretów.

## Test Plan

- `npm run build`
- Feature test renderowania tenant panel response.
