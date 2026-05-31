# Task: Coding Standards

## Cel

Ustalić egzekwowalne standardy kodu dla PHP, TypeScript i dokumentacji.

## Zakres

- Laravel Pint.
- Nazewnictwo modułów.
- Reguły dla Actions, Services, DTO, Resources i Policies.
- Zasady komentarzy i testów.

## Poza Zakresem

- Pełny static analysis level max od pierwszego dnia.
- Refaktory niezwiązane ze standardami.

## Zależności

- `AGENT_CODING_GUIDELINES.md`.

## Kroki

- Dodać komendy jakości do README.
- Ustalić minimalny zestaw checks dla PR.
- Spiąć standardy z roadmapą modułów.

## Subtaski

Brak.

## Acceptance Criteria

- Developer wie, kiedy użyć Action, Service, DTO i Query Object.
- Formatowanie PHP jest jednoznaczne.
- Standardy nie zachęcają do nadmiarowej abstrakcji.

## Test Plan

- `vendor/bin/pint --test`
- `composer test`
