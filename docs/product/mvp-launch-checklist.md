# MVP Launch Checklist

## Required Gates

- tenant isolation tests przechodzą albo są świadomie skipped tylko z powodu braku PostgreSQL w środowisku testowym
- authorization audit bez blockerów
- privacy audit bez blockerów
- backup/restore procedure opisana
- deploy script sprawdzony na staging
- `php artisan aegoryx:preflight` przechodzi na środowisku docelowym
- Horizon albo queue failure monitoring działa
- landlord superadmin utworzony przez `landlord:create`
- public API nie zwraca draftów ani danych prywatnych

## Nice To Have Po MVP

- granularne role tenantowe
- automatyczny purge retention
- automatyczny restore rehearsal
- bardziej szczegółowy billing dashboard

## Post Launch Monitoring

- failed jobs w Horizon
- błędy HTTP 5xx
- błędy public API i rate limiting
- failed payments/webhooks
- security events bez sekretów w payloadach
- wykorzystanie storage i DB

## Decyzja Release

MVP może wystartować tylko wtedy, gdy wszystkie required gates mają właściciela i status `done` albo jawnie zaakceptowane ryzyko z datą ponownego przeglądu.
