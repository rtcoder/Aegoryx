# Queue Operations

Kolejki są podzielone nazwami z `config/aegoryx.php`:

- `system` - zadania globalne i landlord
- `tenant` - zadania wymagające tenant context
- `exports` - eksporty danych i dłuższe prace plikowe
- `default` - fallback dla prostych jobów

## Tenant-aware jobs

Job tenantowy musi mieć `tenantId`. Używaj `App\Support\Queue\InteractsWithTenantContext` i uruchamiaj właściwą logikę przez `runWithTenantContext`.

```php
$job = (new ExampleTenantJob)->forTenant($tenant);
dispatch($job->onQueue(config('aegoryx.queues.tenant')));
```

Trait inicjalizuje `TenancyManager`, wykonuje callback i zawsze resetuje context w `finally`.

## Retry i idempotency

Joby, które tworzą pliki, rekordy billingowe albo eksporty, powinny mieć naturalny klucz idempotencji: UUID requestu, zewnętrzne ID płatności albo unikalny path eksportu. Retry nie może tworzyć duplikatów, jeśli wynik poprzedniej próby został zapisany.

## Worker lokalny

```bash
php artisan queue:work redis --queue=system,tenant,exports,default --tries=3
```

## Horizon

```bash
php artisan horizon
```

Dashboard jest pod `/horizon` i wymaga logowania jako landlord superadmin.
