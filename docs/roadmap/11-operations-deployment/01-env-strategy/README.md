# Task: Env Strategy

## Cel

Opisać konfigurację środowisk dla local, testing, SaaS production i self-hosted.

## Zakres

- Wymagane env vars.
- PostgreSQL/Redis/storage.
- Różnice SaaS vs self-hosted.

## Poza Zakresem

- Sekrety produkcyjne w repo.

## Zależności

- Local dev setup.
- Licensing assumptions.

## Kroki

- Uporządkować `.env.example`.
- Opisać minimalny self-hosted env.
- Dodać ostrzeżenia dla sekretów.

## Subtaski

Brak.

## Acceptance Criteria

- Env docs nie sugerują SQLite jako domyślnego runtime.
- Self-hosted ma jasne minimalne wymagania.
- Sekrety nie są commitowane.

## Test Plan

- Review `.env.example`.
- `php artisan about`.
