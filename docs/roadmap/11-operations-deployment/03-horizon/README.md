# Task: Horizon

## Cel

Wdrożyć Laravel Horizon dla monitoringu kolejek w SaaS/self-hosted.

## Zakres

- Instalacja Horizon.
- Admin-only access.
- Basic queue config.

## Poza Zakresem

- Zaawansowane autoscaling rules.

## Zależności

- Queue setup.
- Admin auth.

## Kroki

- Dodać Horizon dependency i config.
- Ograniczyć dostęp do superadminów.
- Udokumentować uruchamianie.

## Subtaski

Brak.

## Acceptance Criteria

- Horizon nie jest publicznie dostępny.
- Kolejki krytyczne są nazwane.
- Dokumentacja opisuje worker command.

## Test Plan

- Access tests.
- Manual smoke local.
