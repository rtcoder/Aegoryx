# Task: Deploy Commands

## Cel

Ustalić bezpieczną kolejność deployu dla SaaS i self-hosted.

## Zakres

- Maintenance mode.
- `landlord:migrate`.
- `tenants:migrate`.
- Optimize clear/cache.

## Poza Zakresem

- Pełny CI/CD provider.

## Zależności

- Migration commands.
- Env strategy.

## Kroki

- Spisać standardowy deploy.
- Opisać rollback preference: forward-only corrective migrations.
- Dodać checklistę operatorską.

## Subtaski

Brak.

## Acceptance Criteria

- Dokument nie używa plain `php artisan migrate` jako pełnego deployu.
- Deploy zawiera landlord i tenant migrations.
- Production commands używają `--force`.

## Test Plan

- Review deployment docs.
