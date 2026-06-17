# Deploy

Standardowy deploy uruchamia:

```bash
scripts/deploy.sh
```

Skrypt wykonuje:

1. `composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader`
2. `npm ci`
3. `npm run build`
4. `php artisan aegoryx:preflight`
5. `php artisan down`
6. `php artisan optimize:clear`
7. `php artisan landlord:migrate --force`
8. `php artisan tenants:migrate --force`
9. `php artisan db:seed --class=Database\\Seeders\\CommercialPlansSeeder --force`
10. `php artisan optimize`
11. `php artisan horizon:terminate`
12. `php artisan up`
13. `php artisan aegoryx:smoke`
14. `php artisan aegoryx:launch-check`

Jeżeli migracje padną po częściowym sukcesie, skrypt zbiera migracje zakończone statusem `DONE` z bieżącego deployu i cofa tylko je, od końca. W produkcji preferowana strategia naprawy to forward-only corrective migration; rollback jest awaryjny i wymaga przeglądu operatora.

Po deployu:

```bash
php artisan horizon:terminate
```

Horizon uruchomi nowe workery z aktualnym kodem.

## Staging Smoke Test

Po deployu staging/production uruchomić:

```bash
php artisan aegoryx:smoke
php artisan aegoryx:launch-check --with-smoke
```

`aegoryx:smoke` wykonuje `aegoryx:preflight`, sprawdza `/up`, landlord login i opcjonalne URL-e:

- `AEGORYX_SMOKE_TENANT_URL`
- `AEGORYX_SMOKE_PUBLIC_API_URL`

Jeżeli środowisko nie ma przykładowego tenanta albo opublikowanej strony CMS, brak tych URL-i jest świadomym skipem, nie błędem deployu.

`aegoryx:launch-check` zbiera techniczne bramki release: preflight, wymagane dokumenty, brak blockerów w audytach, route public API, ochronę Horizon, aktywnego superadmina i brak failed billing events. W deploy script smoke jest uruchamiany osobno, więc `launch-check` idzie tam bez `--with-smoke`.

## Domain Verification

Zgłoszone domeny tenantów mają status `pending`, dopóki DNS nie zawiera rekordu:

```text
_aegoryx-domain.example.com TXT aegoryx-...
```

Weryfikację można uruchomić ręcznie albo z harmonogramu:

```bash
php artisan tenant-domains:verify
```
