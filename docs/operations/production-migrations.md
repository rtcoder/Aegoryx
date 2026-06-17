# Production Migration Checklist

## Preflight

- backup bazy i storage potwierdzony
- restore rehearsal aktualny albo świadomie zaakceptowane ryzyko
- `APP_ENV=production`
- `APP_DEBUG=false`
- `QUEUE_CONNECTION=redis`
- Redis dostępny dla Horizon
- lista pending landlord migrations przejrzana
- lista pending tenant migrations przejrzana
- liczba tenantów znana
- okno maintenance potwierdzone

## Wykonanie

```bash
scripts/deploy.sh
```

Nie używamy plain `php artisan migrate` jako pełnego deployu. Landlord i tenant migrations są oddzielone celowo.

## Failed Tenant Migration

1. Zatrzymać deploy i zachować pełny output.
2. Sprawdzić, dla którego tenant schema wystąpił błąd.
3. Nie uruchamiać ręcznego rollbacku całej bazy.
4. Preferować forward-only fix migration.
5. Jeżeli rollback jest konieczny, cofać tylko migracje z bieżącego deployu i tylko po potwierdzeniu backupu.
6. Po naprawie uruchomić migrację dla konkretnego tenanta:

```bash
php artisan tenant:migrate tenant-slug --force
```

## Postflight

- `php artisan horizon:terminate`
- sprawdzić logi aplikacji
- sprawdzić failed jobs
- sprawdzić login landlord
- sprawdzić jeden tenant panel
- sprawdzić public API published CMS
