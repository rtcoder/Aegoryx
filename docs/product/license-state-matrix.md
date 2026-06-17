# License State Matrix

| State | Access | Data | Subscription | Operator Action |
| --- | --- | --- | --- | --- |
| trial | allowed | kept | not required | convert or expire |
| active | allowed | kept | required | monitor renewal |
| grace | allowed | kept | required | recover payment or license |
| expired | blocked | kept | required | renew or disable features |
| suspended | blocked | kept | required | manual review required |
| perpetual | allowed | kept | not required | verify installation periodically |

## Zasady

- Expired i suspended nie kasują danych tenantów.
- Perpetual działa bez SaaS subscription.
- Business modules nie czytają payloadów licencji bezpośrednio.
- Entitlements pozostają jedyną warstwą decyzyjną dla feature access.

## SaaS Plan And Limit Matrix

| Plan | Features | Limits | Billing |
| --- | --- | --- | --- |
| starter | CMS, CRM basics, files | `cms.pages=25`, `crm.contacts=500`, `files.storage_mb=1024` | monthly/yearly subscription |
| growth | CMS, CRM, files, public API | `cms.pages=250`, `crm.contacts=5000`, `files.storage_mb=10240`, `public_api.requests_per_minute=120` | monthly/yearly subscription |
| business | CMS, CRM, files, public API, priority support | `cms.pages=unlimited`, `crm.contacts=25000`, `files.storage_mb=102400`, `public_api.requests_per_minute=600` | yearly preferred |
| self_hosted | license payload driven | defined in signed license payload | license subscription/perpetual |

Plan limits are commercial defaults. Runtime decisions still go through `EffectiveEntitlements`, so manual overrides and license payloads can intentionally change limits without business modules reading billing state directly.

## Seeding

Domyślne plany komercyjne są seedowane przez:

```bash
php artisan db:seed --class=Database\\Seeders\\CommercialPlansSeeder --force
```

Seeder jest idempotentny i aktualizuje `plans` oraz `plan_features` dla znanych kluczy planów.
