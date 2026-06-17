# Env Strategy

Aegoryx zakłada PostgreSQL jako runtime dla local, SaaS production i self-hosted. SQLite zostaje wyłącznie narzędziem testowym tam, gdzie test nie wymaga realnego `search_path`.

## Minimalny local

- `APP_URL=http://aegoryx.test`
- `LANDLORD_DOMAIN=admin.aegoryx.test`
- `DB_CONNECTION=pgsql`
- `DB_DATABASE=aegoryx`
- `QUEUE_CONNECTION=redis`
- `FILESYSTEM_DISK=local`

## Minimalny self-hosted

- PHP 8.3+
- PostgreSQL z uprawnieniem do tworzenia schema
- Redis dla cache, queue i Horizon
- trwały storage dla `storage/app`
- poprawnie ustawione `APP_KEY`, domena landlord i domeny tenantów

## Sekrety

Sekrety produkcyjne nie trafiają do repo. Dotyczy to `APP_KEY`, haseł DB, kluczy S3, Paddle, licencji, tokenów webhooków oraz danych SMTP.

## Różnice SaaS vs self-hosted

SaaS zarządza tenantami centralnie i używa wspólnych workerów Horizon. Self-hosted działa jako jedna instalacja z tym samym modelem schema-per-tenant, ale backup, monitoring i rotacja sekretów leżą po stronie operatora instalacji.
