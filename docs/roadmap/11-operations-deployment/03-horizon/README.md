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
- Ograniczyć dostęp do landlord superadminów.
- Udokumentować uruchamianie w `docs/operations/queues.md`.

## Subtaski

Brak.

## Acceptance Criteria

- [x] Horizon nie jest publicznie dostępny.
- [x] Kolejki krytyczne są nazwane.
- [x] Dokumentacja opisuje worker command.

## Test Plan

- [x] Gate access test.
- [x] Config review.
