# CODEX.md — Aegoryx - CMS+CRM SaaS / Self-hosted Platform

Aegoryx is a privacy-first CMS + CRM platform designed for SaaS and self-hosted deployments.
## 1. Cel projektu

Budujemy modularny system **CMS + CRM** z naciskiem na:

* prywatność danych,
* obsługę wielu klientów,
* możliwość działania jako SaaS,
* możliwość działania jako instalacja self-hosted,
* elastyczny system modułów/features per klient,
* model subskrypcyjny i licencyjny,
* publiczne read-only API dla stron WWW,
* osobny panel klienta i osobny panel superadmina,
* przyszłą możliwość migracji z `schema per tenant` do `database per tenant`.

Projekt ma być rozwijany jako produkt komercyjny. Kod powinien być pisany tak, aby można było utrzymywać jeden codebase dla kilku modeli sprzedaży:

1. SaaS hosted by us.
2. Self-hosted subscription.
3. Self-hosted perpetual license.

---

## 2. Stack technologiczny

Preferowany stack:

```txt
Backend:
- Laravel 12+
- PHP 8.3+
- PostgreSQL
- Redis
- Laravel Queue
- Laravel Horizon
- Laravel Sanctum
- Laravel Fortify albo Jetstream
- Laravel Socialite
- Laravel Cashier Paddle

Frontend paneli:
- Inertia
- Vue 3 albo React
- TypeScript
- Tailwind CSS

Storage:
- S3-compatible storage
- local disk tylko dla dev

Publiczne strony:
- API-read z Laravel
- frontend strony klienta konsumuje read-only API
- docelowo możliwy osobny frontend typu Astro / Nuxt / Next / SvelteKit
```

Domyślnie panel administracyjny i panel klienta robimy przez **Laravel + Inertia**.

Publiczne strony klientów nie powinny być sprzęgnięte z panelem. Powinny korzystać z osobnego read-only API.

---

## 3. Najważniejsze decyzje architektoniczne

### 3.1 Multi-tenancy

Na start wybieramy:

```txt
PostgreSQL schema per tenant
```

Nie wybieramy na start:

```txt
database per tenant
```

Powód:

* `database per tenant` daje najlepszą izolację, ale jest trudniejsze operacyjnie.
* Projekt na start będzie utrzymywany bez osobnego DevOpsa.
* `schema per tenant` jest kompromisem między izolacją a prostotą utrzymania.
* W przyszłości można migrować konkretnego tenanta ze schemy do osobnej bazy.

Docelowy model:

```txt
Jedna fizyczna baza PostgreSQL

public schema:
- landlord/system data
- tenants
- domains
- features
- plans
- subscriptions
- licenses
- global identities
- superadmin data

tenant_x schema:
- CMS data
- CRM data
- tenant users/memberships
- tenant files metadata
- tenant audit logs
```

Przykład:

```txt
public.tenants
public.tenant_domains
public.tenant_features
public.features
public.plans
public.subscriptions
public.system_installation
public.system_license

tenant_acme.users
tenant_acme.cms_pages
tenant_acme.cms_page_revisions
tenant_acme.published_pages
tenant_acme.crm_contacts
tenant_acme.crm_deals
tenant_acme.files
tenant_acme.audit_logs
```

---

## 4. Zasady multi-tenancy

### 4.1 Landlord vs tenant

Dane systemowe trzymamy w schemie `public`.

Dane biznesowe klienta trzymamy w jego schemie tenantowej.

Nie wolno mieszać danych biznesowych klienta w tabelach `public`, chyba że są to dane techniczne, billingowe, licencyjne albo agregaty.

### 4.2 Tenant context

Cały dostęp do danych tenantowych musi przechodzić przez centralny mechanizm tenancy.

Przykładowa warstwa:

```php
Tenancy::initialize($tenant);
Tenancy::end();
```

Nie wolno ustawiać `search_path` ręcznie w kontrolerach, jobach, serwisach albo modelach.

Dozwolone miejsce ustawiania tenant context:

* middleware,
* tenancy manager,
* command tenant runner,
* queue job bootstrap,
* test helper.

### 4.3 Search path

Dla aktywnego tenanta PostgreSQL powinien mieć ustawiony search path:

```sql
SET search_path TO tenant_acme, public;
```

Kod aplikacji nie powinien kwalifikować każdej tabeli schemą ręcznie. Modele tenantowe powinny działać normalnie po ustawieniu contextu.

### 4.4 Brak cross-schema foreign key

Unikamy FK między schemą tenantową a `public`.

Nie robić:

```txt
tenant_acme.users.identity_id -> public.identities.id
```

Powód:

* utrudnia migrację tenanta do osobnej bazy,
* komplikuje dump/restore,
* mocniej sprzęga tenant data z landlord data.

Dopuszczalne:

```txt
tenant_acme.users.identity_id
```

ale walidacja relacji po stronie aplikacji.

### 4.5 `tenant_id` w tabelach

W tabelach stricte tenantowych nie dodajemy `tenant_id`, bo izolację daje schema.

Przykład:

```txt
tenant_acme.crm_contacts
- id
- first_name
- last_name
- email_encrypted
- phone_encrypted
```

Nie:

```txt
tenant_acme.crm_contacts
- tenant_id
- id
- ...
```

Wyjątki, gdzie `tenant_id` jest wskazany:

* tabele globalne,
* billing usage,
* kolejki,
* outbox/events,
* global audit,
* logi systemowe,
* indeksy wyszukiwawcze,
* storage metadata poza schemą tenantową.

---

## 5. Struktura domen/subdomen

Zakładane domeny:

```txt
panel.example.com       -> panel klienta
admin.example.com       -> panel superadmina
api.example.com         -> publiczne read-only API CMS
client-domain.com       -> publiczna strona klienta
```

Tenant może być rozpoznawany po:

* subdomenie,
* domenie własnej klienta,
* nagłówku,
* parametrze tylko w trybie developerskim.

Tabela:

```txt
tenant_domains
- id
- tenant_id
- domain
- type
- is_primary
- verified_at
- created_at
- updated_at
```

Przykładowe `type`:

```txt
panel
api
website
custom
```

---

## 6. Panele aplikacji

### 6.1 Panel klienta

Panel klienta działa pod:

```txt
panel.example.com
```

albo pod subdomeną klienta:

```txt
client.panel.example.com
```

Panel klienta obsługuje:

* CMS,
* CRM,
* użytkowników klienta,
* role i uprawnienia,
* pliki,
* ustawienia strony,
* integracje,
* billing status, jeśli SaaS,
* status licencji, jeśli self-hosted.

### 6.2 Panel superadmina

Panel superadmina działa pod:

```txt
admin.example.com
```

Superadmin zarządza:

* tenantami,
* domenami tenantów,
* features,
* planami,
* subskrypcjami,
* licencjami,
* statusem klientów,
* support access,
* audytem systemowym.

Superadmin nie powinien mieć nieaudytowanego dostępu do danych tenantów.

Jeżeli superadmin wchodzi do tenanta w trybie support:

* wymagane 2FA,
* wymagany powód wejścia,
* wymagany audit log,
* powinien być widoczny banner trybu support,
* sesja support powinna mieć czas ważności.

---

## 7. CMS

CMS ma obsługiwać:

* strony,
* podstrony,
* menu/nawigację,
* bloki,
* media,
* rewizje,
* drafty,
* publikacje,
* SEO metadata,
* redirecty,
* ustawienia strony,
* opcjonalnie blog/news.

Nie wystawiamy draftów publicznie.

### 7.1 Model publikacji

Dane edytowalne i dane publiczne muszą być rozdzielone.

Przykład:

```txt
cms_pages
cms_page_revisions
cms_blocks
published_pages
published_routes
```

Publiczne API czyta tylko:

```txt
published_pages
published_routes
public media
```

Nie czyta bezpośrednio draftów.

### 7.2 Read-only API

Publiczne API CMS musi być read-only.

Przykładowe endpointy:

```txt
GET /api/read/page?path=/o-nas
GET /api/read/navigation
GET /api/read/settings
GET /api/read/posts
GET /api/read/posts/{slug}
```

Zasady:

* brak dostępu do CRM,
* brak danych prywatnych,
* tylko published content,
* cache,
* rate limit,
* CORS allow-list,
* brak endpointów modyfikujących dane.

---

## 8. CRM

CRM ma przechowywać dane wrażliwe. Projektować z założeniem prywatności.

Moduły CRM:

* kontakty,
* firmy,
* deale/szanse sprzedaży,
* notatki,
* zadania,
* pipeline,
* custom fields,
* import/export,
* historia aktywności,
* przypisania do użytkowników,
* załączniki.

Dane CRM nie mogą być dostępne przez publiczne CMS read API.

---

## 9. Szyfrowanie danych

### 9.1 Dane wrażliwe

Szyfrujemy pola wrażliwe, np.:

* prywatny telefon,
* prywatny email, jeśli nie musi być wyszukiwany tekstowo,
* adres,
* notatki CRM,
* tokeny integracji,
* OAuth refresh tokens,
* 2FA secrets,
* recovery codes,
* custom fields oznaczone jako sensitive.

Nie szyfrujemy bezrefleksyjnie wszystkiego.

Pola używane do filtrowania/sortowania/wyszukiwania wymagają osobnego podejścia.

### 9.2 Hash / blind index

Dla pól, po których trzeba robić exact match, stosujemy dodatkowe pole hash.

Przykład:

```txt
email_encrypted
email_hash
phone_encrypted
phone_hash
```

Zasady:

* encrypted field służy do odczytu,
* hash field służy do exact match,
* nie używać encrypted field do `LIKE`,
* nie próbować sortować po encrypted field.

### 9.3 Klucze

Na start można użyć Laravel encryption.

Docelowo projekt powinien umożliwiać per-tenant encryption key.

Model docelowy:

```txt
master key / external KMS
  -> tenant data key
    -> field encryption
```

Nie hardcodować logiki w taki sposób, aby później nie dało się przejść na per-tenant keys.

---

## 10. Auth i security

### 10.1 Logowanie

System ma obsługiwać:

* email/password,
* 2FA TOTP,
* recovery codes,
* OTP jako opcjonalny fallback,
* logowanie przez Google/Facebook/inne providery OAuth,
* zarządzanie aktywnymi sesjami.

Preferowane pakiety:

```txt
Laravel Fortify / Jetstream
Laravel Sanctum
Laravel Socialite
```

### 10.2 2FA

2FA jest wymagane dla:

* superadminów,
* ownerów tenantów,
* użytkowników z wysokimi uprawnieniami.

2FA może być opcjonalne dla zwykłych użytkowników, ale system powinien pozwalać tenantowi wymusić 2FA dla całej organizacji.

### 10.3 Recovery codes

Recovery codes:

* muszą być generowane jako jednorazowe,
* muszą być przechowywane bezpiecznie,
* po użyciu kod musi być unieważniony,
* regeneracja kodów wymaga potwierdzenia hasła albo aktywnego 2FA.

### 10.4 OAuth

Logowanie przez Google/Facebook/inne:

* nie może omijać 2FA,
* po udanym OAuth nadal sprawdzamy wymagania 2FA,
* tokeny OAuth przechowujemy zaszyfrowane,
* provider accounts trzymamy w osobnej tabeli.

Przykład:

```txt
social_accounts
- id
- identity_id
- provider
- provider_user_id
- email
- access_token_encrypted
- refresh_token_encrypted
- expires_at
```

---

## 11. Identity, użytkownicy i membership

System powinien rozróżniać:

```txt
global identity
tenant user / membership
```

Przykład:

```txt
public.identities
- id
- email
- password
- two_factor_secret
- created_at
- updated_at

tenant_acme.users
- id
- identity_id
- display_name
- role
- status
- created_at
- updated_at
```

Jedna osoba może mieć dostęp do wielu tenantów.

Nie zakładać, że `identity_id` jest FK do `public.identities`. Trzymać jako logical reference.

---

## 12. Role i uprawnienia

System powinien mieć:

* role,
* permissions,
* policies,
* feature checks.

Przykładowe role:

```txt
superadmin
tenant_owner
tenant_admin
editor
crm_manager
crm_user
viewer
api_consumer
```

Features i permissions to nie to samo.

```txt
Feature:
- czy klient ma moduł CRM?

Permission:
- czy ten konkretny user może usuwać kontakty?
```

Przykład:

```txt
feature: crm.contacts
permission: crm.contacts.view
permission: crm.contacts.create
permission: crm.contacts.delete
```

Kod powinien sprawdzać oba poziomy:

```php
Entitlements::allows('crm.contacts');
Gate::authorize('crm.contacts.view');
```

---

## 13. Features / Entitlements

Nie robimy prostych flag rozrzuconych po kodzie.

Centralna warstwa:

```php
Entitlements::allows('crm.contacts');
Entitlements::limit('users.max');
Entitlements::config('crm.custom_fields');
```

Features mogą pochodzić z:

* planu SaaS,
* ręcznego override,
* licencji self-hosted,
* triala,
* internal/demo mode.

Tabela:

```txt
features
- id
- key
- name
- description
- created_at
- updated_at
```

Przykładowe feature keys:

```txt
cms.pages
cms.blog
cms.media
cms.redirects
api.read
crm.contacts
crm.companies
crm.deals
crm.tasks
crm.notes
crm.custom_fields
crm.import
crm.export
files.private_storage
integrations.google
integrations.facebook
```

---

## 14. Billing, licencje i modele sprzedaży

System musi wspierać 3 modele:

```txt
1. SaaS Cloud
2. Self-hosted Annual
3. Self-hosted Perpetual
```

Nie robić jednej flagi `standalone`.

Zamiast tego stosować osobne pojęcia:

```txt
deployment_type
billing_model
license_type
license_status
```

### 14.1 Enumy

```php
enum DeploymentType: string
{
    case Saas = 'saas';
    case SelfHosted = 'self_hosted';
}

enum BillingModel: string
{
    case Subscription = 'subscription';
    case OneTime = 'one_time';
    case None = 'none';
    case Manual = 'manual';
}

enum LicenseType: string
{
    case SaasSubscription = 'saas_subscription';
    case SelfHostedSubscription = 'self_hosted_subscription';
    case Perpetual = 'perpetual';
    case Trial = 'trial';
    case Internal = 'internal';
}

enum LicenseStatus: string
{
    case Active = 'active';
    case Grace = 'grace';
    case Expired = 'expired';
    case Suspended = 'suspended';
    case Revoked = 'revoked';
}
```

### 14.2 SaaS Cloud

```txt
deployment_type = saas
billing_model = subscription
license_type = saas_subscription
```

Płatności przez:

```txt
Laravel Cashier Paddle
```

Paddle jest preferowany, bo może działać jako Merchant of Record dla sprzedaży cyfrowej.

Cashier nie powinien być używany bezpośrednio w modułach biznesowych.

Nie robić:

```php
$user->subscribed('default')
```

w wielu miejscach systemu.

Robić:

```php
Entitlements::allows('crm.contacts');
```

Cashier służy do:

* płatności,
* subskrypcji,
* webhooków,
* synchronizacji statusu billingowego.

Entitlements służą do decyzji aplikacyjnych.

### 14.3 Self-hosted Annual

```txt
deployment_type = self_hosted
billing_model = subscription
license_type = self_hosted_subscription
```

Lokalna instalacja klienta nie powinna zależeć bezpośrednio od Cashiera.

Cashier działa po stronie naszego license servera.

Lokalna aplikacja:

* ma license key,
* może robić online activation,
* ma grace period,
* ma lokalny cache statusu licencji,
* po wygaśnięciu nie powinna bezpiecznie blokować dostępu do danych.

Po wygaśnięciu licencji preferowany tryb:

* dane nadal dostępne,
* eksport danych działa,
* ostrzeżenie w panelu,
* brak aktualizacji,
* brak supportu,
* możliwe ograniczenie tworzenia nowych rekordów/użytkowników.

### 14.4 Self-hosted Perpetual

```txt
deployment_type = self_hosted
billing_model = one_time
license_type = perpetual
```

Zasada:

```txt
Klient kupuje prawo używania systemu bezterminowo.
Aktualizacje są dostępne tylko do updates_until.
Support jest dostępny tylko do support_until.
```

Nie sprzedawać “wszystkiego na zawsze”.

Sprzedawać:

```txt
perpetual usage + limited maintenance window
```

---

## 15. LicenseService

Wymagany centralny serwis:

```php
LicenseService::status();
LicenseService::isActive();
LicenseService::isExpired();
LicenseService::updatesUntil();
LicenseService::supportUntil();
LicenseService::allowsOffline();
```

Dla self-hosted license może pochodzić z:

* online license server,
* signed offline license file.

### 15.1 Offline license file

Obsługiwać docelowo format:

```json
{
  "payload": {
    "license_type": "perpetual",
    "customer_name": "ACME Sp. z o.o.",
    "valid_until": null,
    "updates_until": "2027-05-31",
    "support_until": "2027-05-31",
    "max_tenants": 1,
    "max_users": 10,
    "features": {
      "cms.pages": true,
      "crm.contacts": true,
      "crm.deals": false,
      "api.read": true
    }
  },
  "signature": "..."
}
```

Podpis weryfikować publicznym kluczem.

Nie trzymać prywatnego klucza podpisującego w aplikacji klienta.

---

## 16. Struktura bazowa tabel landlord

Minimalne tabele landlord:

```txt
tenants
tenant_domains
features
plans
plan_features
tenant_features
subscriptions
licenses
system_installations
identities
super_admins
support_sessions
audit_superadmin_actions
```

### 16.1 tenants

```txt
id
name
slug
schema_name
status
deployment_type
billing_model
license_type
created_at
updated_at
```

### 16.2 tenant_domains

```txt
id
tenant_id
domain
type
is_primary
verified_at
created_at
updated_at
```

### 16.3 features

```txt
id
key
name
description
created_at
updated_at
```

### 16.4 plans

```txt
id
code
name
provider
provider_product_id
provider_monthly_price_id
provider_yearly_price_id
limits_json
created_at
updated_at
```

### 16.5 plan_features

```txt
id
plan_id
feature_key
enabled
limits_json
config_json
created_at
updated_at
```

### 16.6 tenant_features

```txt
id
tenant_id
feature_key
enabled
source
limits_json
config_json
created_at
updated_at
```

`source`:

```txt
plan
manual_override
license
trial
internal
```

### 16.7 system_installations

```txt
id
uuid
deployment_type
license_type
app_version
installed_at
last_seen_at
created_at
updated_at
```

### 16.8 licenses

```txt
id
installation_id
license_type
status
license_key_hash
payload_json
valid_from
valid_until
grace_until
updates_until
support_until
last_verified_at
created_at
updated_at
```

---

## 17. Struktura tenant schema

Minimalne tabele tenantowe:

```txt
users
roles
permissions
role_user
cms_pages
cms_page_revisions
cms_blocks
published_pages
published_routes
crm_contacts
crm_companies
crm_deals
crm_notes
crm_tasks
files
audit_logs
```

Nie dodawać `tenant_id` do tych tabel, chyba że istnieje konkretny powód techniczny.

---

## 18. Migracje

Migracje dzielimy na:

```txt
database/migrations/landlord
database/migrations/tenant
```

Komendy:

```txt
php artisan migrate:landlord
php artisan tenants:migrate
php artisan tenants:rollback
php artisan tenants:seed
```

Każda migracja tenantowa musi być możliwa do wykonania dla każdej schemy tenantowej.

Nie zakładać jednej aktywnej schemy podczas migracji.

---

## 19. Kolejki i joby

Każdy job pracujący na danych tenantowych musi zawierać `tenant_id`.

Nie robić:

```php
ProcessImport::dispatch($fileId);
```

Robić:

```php
ProcessImport::dispatch($tenantId, $fileId);
```

W jobie:

```php
public function handle(): void
{
    $tenant = Tenant::findOrFail($this->tenantId);

    Tenancy::initialize($tenant);

    try {
        // tenant work
    } finally {
        Tenancy::end();
    }
}
```

Nie zakładać, że tenant context z requesta istnieje w jobie.

---

## 20. Pliki i storage

Pliki prywatne nie mogą być publiczne.

Storage:

```txt
S3-compatible storage
private by default
signed URLs for private downloads
```

Prefixy:

```txt
tenant/{tenant_id}/private/files/{uuid}
tenant/{tenant_id}/public/assets/{uuid}
tenant/{tenant_id}/cms/media/{uuid}
```

Dla CRM:

* download tylko przez autoryzowany endpoint,
* policy check,
* audit log,
* temporary signed URL.

Nie zapisywać w CRM stałych publicznych URL-i do prywatnych plików.

---

## 21. Audit log

System musi audytować:

* logowania,
* błędne logowania,
* zmiany hasła,
* zmiany 2FA,
* użycie recovery code,
* wejście superadmina do tenanta,
* eksport danych,
* pobranie prywatnych plików,
* zmianę features,
* zmianę licencji,
* zmianę planu,
* zmianę uprawnień,
* masowe importy/usunięcia.

Tabela tenantowa:

```txt
audit_logs
- id
- actor_identity_id
- actor_user_id
- action
- resource_type
- resource_id
- metadata_json
- ip
- user_agent
- created_at
```

Tabela globalna:

```txt
audit_superadmin_actions
- id
- actor_id
- tenant_id
- action
- reason
- metadata_json
- ip
- user_agent
- created_at
```

---

## 22. API i zasady bezpieczeństwa

### 22.1 Panel API

Panel API:

* wymaga auth,
* wymaga tenant context,
* wymaga feature checks,
* wymaga policies,
* nie może zwracać danych innego tenanta.

### 22.2 Public read API

Public API:

* tylko read-only,
* tylko published content,
* cache,
* rate limit,
* brak CRM,
* brak danych użytkowników,
* brak draftów.

### 22.3 Rate limiting

Wymagane rate limiting dla:

* login,
* password reset,
* 2FA challenge,
* public API,
* import/export,
* OAuth callback.

---

## 23. Kod — zasady ogólne

### 23.1 Nie rozrzucać logiki biznesowej po kontrolerach

Kontrolery mają być cienkie.

Używać:

* Actions,
* Services,
* DTO,
* Form Requests,
* Policies,
* Events,
* Jobs.

### 23.2 Nie robić raw SQL bez potrzeby

Raw SQL dozwolony, jeżeli:

* jest potrzebny wydajnościowo,
* jest dobrze zamknięty w repository/query object,
* ma testy,
* nie obchodzi tenant context.

### 23.3 Nie robić bezpośrednich checków billingowych

Nie wolno w modułach pisać:

```php
$tenant->subscription->active()
```

Używać:

```php
Entitlements::allows('feature.key')
```

### 23.4 Nie używać `standalone`

Nie dodawać prostego pola:

```txt
standalone = true
```

Zamiast tego używać:

```txt
deployment_type
billing_model
license_type
license_status
```

---

## 24. Proponowana struktura katalogów

```txt
app/
  Actions/
    Landlord/
    Tenant/
    Cms/
    Crm/
    Billing/
    Licensing/

  Domains/
    Tenancy/
    Entitlements/
    Licensing/
    Billing/
    Cms/
    Crm/
    Identity/
    Security/
    Audit/

  Http/
    Controllers/
      Admin/
      Panel/
      Api/
      PublicRead/
    Middleware/
    Requests/

  Models/
    Landlord/
    Tenant/

  Services/
    Tenancy/
    Entitlements/
    Licensing/
    Encryption/
    Billing/

  Jobs/
    Tenant/

  Policies/

  Enums/

database/
  migrations/
    landlord/
    tenant/
  seeders/
    landlord/
    tenant/
```

---

## 25. MVP zakres

Pierwszy MVP powinien zawierać:

### 25.1 Core

* landlord schema,
* tenant creation,
* schema creation,
* tenant context,
* tenant migrations,
* panel superadmina,
* panel klienta,
* login,
* 2FA foundation,
* features foundation,
* entitlements foundation.

### 25.2 CMS MVP

* strony,
* bloki,
* draft/publish,
* published pages,
* read-only API,
* media publiczne.

### 25.3 CRM MVP

* kontakty,
* firmy,
* notatki,
* podstawowe szyfrowanie pól,
* audit log.

### 25.4 Billing/licensing MVP

* enumy deployment/billing/license,
* plan/features,
* manual subscription status,
* Paddle integration może być w kolejnym kroku,
* LicenseService placeholder.

Nie budować wszystkiego naraz. Najpierw core tenancy + auth + entitlements.

---

## 26. Testy

Wymagane testy:

### 26.1 Tenancy

* tenant A nie widzi danych tenant B,
* request z domeny A ustawia schemę A,
* request z domeny B ustawia schemę B,
* job z tenant_id wykonuje się w poprawnej schemie,
* brak tenant context blokuje dostęp do modeli tenantowych.

### 26.2 Features

* feature disabled blokuje route,
* feature enabled pozwala na dostęp,
* limit users działa,
* manual override nadpisuje plan.

### 26.3 Auth/security

* 2FA challenge działa,
* recovery code jest jednorazowy,
* superadmin support session wymaga powodu,
* impersonation zapisuje audit log.

### 26.4 CMS

* draft nie jest widoczny w public API,
* published page jest widoczny w public API,
* unpublish usuwa/ukrywa publiczny snapshot.

### 26.5 CRM

* sensitive fields są szyfrowane,
* hash exact match działa,
* user bez permission nie widzi kontaktu,
* eksport zapisuje audit log.

---

## 27. Komendy artisan do zaimplementowania

```txt
php artisan tenants:create
php artisan tenants:migrate
php artisan tenants:rollback
php artisan tenants:seed
php artisan tenants:list
php artisan tenants:run

php artisan features:sync
php artisan license:status
php artisan license:verify
php artisan license:install

php artisan cms:publish-page
php artisan cms:rebuild-public-cache
```

---

## 28. Konfiguracja `.env`

Przykład:

```env
APP_DEPLOYMENT_TYPE=saas
APP_BILLING_PROVIDER=paddle

TENANCY_MODE=schema
TENANCY_LANDLORD_SCHEMA=public

LICENSE_MODE=online
LICENSE_SERVER_URL=https://license.example.com

QUEUE_CONNECTION=redis
CACHE_STORE=redis
SESSION_DRIVER=redis

FILESYSTEM_DISK=s3
```

Dla self-hosted:

```env
APP_DEPLOYMENT_TYPE=self_hosted
APP_BILLING_PROVIDER=none

TENANCY_MODE=schema
TENANCY_LANDLORD_SCHEMA=public

LICENSE_MODE=offline
LICENSE_FILE_PATH=storage/license/license.json
```

---

## 29. Rzeczy, których unikać

Nie robić:

* jednego wielkiego `tenant_id` we wszystkich tabelach jako głównego modelu multi-tenancy,
* `standalone = true`,
* checków subskrypcji rozsianych po aplikacji,
* cross-schema foreign key,
* publicznych URL-i do prywatnych plików CRM,
* draftów CMS w publicznym API,
* ręcznego ustawiania `search_path` poza Tenancy Managerem,
* zakładania, że job ma tenant context,
* trzymania tokenów OAuth plaintextem,
* logowania danych wrażliwych,
* mieszania superadmin panelu z panelem klienta jako jednej przestrzeni.

---

## 30. Definition of Done dla dużych funkcji

Każda większa funkcja jest ukończona tylko jeśli:

* ma migracje landlord/tenant w odpowiednim miejscu,
* respektuje tenant context,
* ma feature/entitlement check, jeśli dotyczy,
* ma permission/policy check, jeśli dotyczy,
* ma testy,
* nie wycieka danych między tenantami,
* nie wystawia danych prywatnych przez public API,
* zapisuje audit log dla akcji krytycznych,
* działa w SaaS i nie blokuje przyszłego self-hosted,
* nie używa bezpośrednio Cashiera do decyzji feature access.

---

## 31. Priorytety implementacyjne

Kolejność prac:

```txt
1. Laravel base + auth foundation
2. PostgreSQL schema tenancy
3. landlord models: Tenant, Domain, Feature
4. tenant creation + schema creation
5. tenant migrations runner
6. tenant resolving by domain/subdomain
7. Entitlements foundation
8. Admin panel foundation
9. Tenant panel foundation
10. CMS MVP
11. Public read API
12. CRM MVP
13. Encryption of sensitive fields
14. Audit logs
15. Billing/licensing abstraction
16. Paddle/Cashier integration
17. Self-hosted license support
```

---

## 32. Najważniejsza zasada projektu

Ten system ma być projektowany tak, aby moduły biznesowe nie wiedziały, czy klient działa jako:

```txt
SaaS subscription
Self-hosted subscription
Self-hosted perpetual
Trial
Manual enterprise deal
```

Moduły mają pytać wyłącznie:

```php
Entitlements::allows('feature.key');
Entitlements::limit('limit.key');
```

Tenant context ma być ustawiany centralnie.

Dane klienta mają być fizycznie separowane przez PostgreSQL schema.

Publiczne API ma czytać wyłącznie opublikowane dane CMS.

CRM i dane wrażliwe mają być prywatne, audytowane i szyfrowane tam, gdzie ma to sens.
