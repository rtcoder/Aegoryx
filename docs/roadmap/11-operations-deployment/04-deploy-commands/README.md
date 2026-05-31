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

- Utrzymywać standardowy deploy w `scripts/deploy.sh`.
- Opisać rollback preference: forward-only corrective migrations.
- Dodać checklistę operatorską.

## Skrypt

Domyślna komenda:

```bash
scripts/deploy.sh
```

Skrypt zawsze wykonuje `composer install`, `npm ci`, `npm run build`, `landlord:migrate` i `tenants:migrate`.

Jeżeli deploy padnie po tym, jak część migracji zakończy się statusem `DONE`, skrypt zbiera ich nazwy z outputu migracji i wywołuje rollback tylko dla tych migracji, w odwrotnej kolejności:

```bash
php artisan landlord:migrate:rollback --migration=...
php artisan tenants:migrate:rollback --schema=... --migration=...
```

Jeżeli w danym deployu nie przeszła żadna migracja, rollback migracji jest pomijany.

## Subtaski

Brak.

## Acceptance Criteria

- Dokument nie używa plain `php artisan migrate` jako pełnego deployu.
- Deploy zawiera landlord i tenant migrations.
- Production commands używają `--force`.

## Test Plan

- Review deployment docs.
