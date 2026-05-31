# Task: Local Dev Setup

## Cel

Opisać i ustabilizować lokalne uruchamianie aplikacji z PostgreSQL, Redis, kolejkami i frontendem.

## Zakres

- Wymagane wersje PHP, Composer, Node, PostgreSQL i Redis.
- Przykładowe `.env`.
- Komendy setupu i uruchomienia.

## Poza Zakresem

- Produkcyjny deployment.
- Docker jako wymóg, chyba że zostanie wybrany później.

## Zależności

- Laravel app.
- Decyzja o lokalnej bazie `aegoryx`.

## Kroki

- Uzupełnić README projektu o local setup.
- Dodać sekcję migracji landlord/tenant.
- Opisać najczęstsze problemy.

## Subtaski

Brak.

## Acceptance Criteria

- Nowy developer może uruchomić projekt lokalnie z dokumentacji.
- PostgreSQL jest domyślną bazą.
- Komendy nie sugerują plain `php artisan migrate` jako pełnego deployu.

## Test Plan

- Przejść instrukcję na czystym checkout.
- Zweryfikować `php artisan about`.
