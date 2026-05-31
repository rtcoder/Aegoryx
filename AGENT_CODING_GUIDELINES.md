# AGENT_CODING_GUIDELINES.md — Aegoryx

## 1. Cel dokumentu

Ten dokument opisuje standard pisania kodu dla projektu **Aegoryx**.

Aegoryx to privacy-first **CMS + CRM** budowany jako modular monolith w Laravelu, z obsługą:

- PostgreSQL schema-per-tenant,
- SaaS deployment,
- self-hosted deployment,
- licencji subskrypcyjnych i perpetual,
- publicznego read-only CMS API,
- prywatnego CRM,
- szyfrowania danych wrażliwych,
- audytu działań,
- historii zmian każdego istotnego elementu.

Agent pracujący nad projektem musi traktować te zasady jako obowiązujące. Nie są to sugestie.

---

## 2. Główne zasady projektowe

Kod ma być:

- czytelny,
- testowalny,
- modularny,
- bezpieczny,
- przygotowany pod multi-tenancy,
- przygotowany pod SaaS i self-hosted,
- możliwy do utrzymania przez mały zespół,
- bez niepotrzebnej abstrakcji.

Stosujemy:

```txt
DRY   — Don't Repeat Yourself
KISS  — Keep It Simple, Stupid
YAGNI — You Aren't Gonna Need It
SOLID — tam, gdzie realnie pomaga
```

Nie tworzymy nadmiarowej architektury tylko dlatego, że “może się kiedyś przyda”.  
Jednocześnie nie piszemy kodu jednorazowego, który rozwali się przy pierwszym module więcej.

---

## 3. Architektura: modular monolith

Aegoryx ma być **modular monolith**, nie mikroserwisami.

Każdy większy obszar domenowy ma mieć własny moduł/katalog:

```txt
app/Modules/
  Tenancy/
  Identity/
  Auth/
  Security/
  Entitlements/
  Licensing/
  Billing/
  Cms/
  Crm/
  Files/
  Audit/
  PublicApi/
  AdminConsole/
  TenantPanel/
```

Moduł powinien zawierać własne:

```txt
Actions/
DTO/
Enums/
Events/
Http/
  Controllers/
  Requests/
  Resources/
Jobs/
Models/
Policies/
Queries/
Routes/
Services/
Support/
```

Przykład:

```txt
app/Modules/Crm/
  Actions/
    CreateContactAction.php
    UpdateContactAction.php
    DeleteContactAction.php
  DTO/
    ContactData.php
  Enums/
    ContactStatus.php
  Events/
    ContactCreated.php
    ContactUpdated.php
  Http/
    Controllers/
      ContactController.php
    Requests/
      StoreContactRequest.php
      UpdateContactRequest.php
    Resources/
      ContactResource.php
  Jobs/
  Models/
    Contact.php
  Policies/
    ContactPolicy.php
  Queries/
    ContactIndexQuery.php
  Services/
  Routes/
    web.php
    api.php
```

Moduły mogą komunikować się przez:

- publiczne serwisy modułu,
- Actions,
- Events,
- DTO,
- kontrakty/interfejsy, jeśli realnie potrzebne.

Nie wolno robić chaotycznych zależności między modułami.

---

## 4. Kontrolery, Actions, Services

### 4.1 Kontrolery

Kontrolery mają być cienkie.

Kontroler powinien:

- odebrać request,
- użyć FormRequest do walidacji,
- sprawdzić policy albo middleware,
- wywołać Action/Service,
- zwrócić response/resource/redirect.

Kontroler nie powinien zawierać skomplikowanej logiki biznesowej.

Dobrze:

```php
public function store(StoreContactRequest $request, CreateContactAction $action)
{
    $contact = $action->handle(ContactData::fromRequest($request));

    return redirect()
        ->route('crm.contacts.show', $contact)
        ->with('success', 'Contact created.');
}
```

Źle:

```php
public function store(Request $request)
{
    // 200 linii walidacji, tworzenia, audytu, synchronizacji, notyfikacji
}
```

### 4.2 Actions

Używamy Action dla konkretnych operacji biznesowych:

```txt
CreateContactAction
UpdateContactAction
PublishPageAction
EnableTenantFeatureAction
VerifyLicenseAction
```

Action powinien mieć jedną odpowiedzialność.

Action może:

- modyfikować dane,
- odpalać eventy,
- pisać historię,
- koordynować kilka serwisów,
- wykonywać transakcję.

### 4.3 Services

Service stosujemy, gdy logika jest współdzielona albo reprezentuje większy mechanizm:

```txt
TenancyManager
EntitlementService
LicenseService
AuditLogger
EncryptionService
PublishedPageBuilder
```

Nie każdy kawałek kodu musi być serwisem.

---

## 5. Modele i tabele

### 5.1 Soft deletes

W głównych tabelach systemu używamy **soft deletes**.

Dotyczy to przede wszystkim:

- tenantów,
- użytkowników,
- klientów CRM,
- firm CRM,
- deali,
- notatek,
- zadań,
- stron CMS,
- wpisów CMS,
- bloków CMS,
- plików,
- domen,
- licencji,
- planów,
- features przypisanych do tenantów,
- obiektów biznesowych, które użytkownik może usunąć z panelu.

Przykład migracji:

```php
Schema::create('crm_contacts', function (Blueprint $table) {
    $table->id();
    $table->string('first_name')->nullable();
    $table->string('last_name')->nullable();
    $table->timestamps();
    $table->softDeletes();
});
```

Przykład modelu:

```php
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use SoftDeletes;
}
```

Nie używać hard delete dla danych biznesowych bez bardzo dobrego powodu.

Hard delete jest dopuszczalny dla:

- tabel pivot bez wartości biznesowej,
- cache,
- tabel tymczasowych,
- technicznych kolejek,
- danych wygenerowanych, które można odtworzyć,
- trwałego usuwania po procesie GDPR/data retention, jeśli jest jawnie wymagane.

### 5.2 Restore

Jeżeli tabela ma soft delete, projektując usuwanie należy rozważyć również:

- czy element można przywrócić,
- kto może go przywrócić,
- czy przywrócenie zapisuje historię,
- co z relacjami zależnymi,
- czy public API ma ignorować soft-deleted records.

### 5.3 Status zamiast usuwania

Jeżeli obiekt ma lifecycle biznesowy, często lepszy jest status niż delete.

Przykład:

```txt
draft
published
archived
disabled
suspended
```

Soft delete nie zastępuje statusu biznesowego.

---

## 6. Historia zmian i audyt

### 6.1 Obowiązkowa historia działań

Każde istotne działanie w systemie musi mieć wpis w historii danego elementu.

Dotyczy to:

- utworzenia,
- edycji,
- usunięcia,
- przywrócenia,
- publikacji,
- odpublikowania,
- zmiany statusu,
- zmiany właściciela,
- zmiany uprawnień,
- zmiany features,
- eksportu,
- importu,
- pobrania prywatnego pliku,
- użycia trybu support/superadmin,
- zmian billing/licensing,
- zmian security.

Nie wystarczy globalny log techniczny. Użytkownik powinien móc zobaczyć historię konkretnego elementu.

### 6.2 Historia per element

Każdy główny model powinien mieć historię aktywności.

Przykład tabeli:

```txt
activity_entries
- id
- subject_type
- subject_id
- actor_type
- actor_id
- action
- description
- before_json
- after_json
- metadata_json
- ip
- user_agent
- created_at
```

Przykład `action`:

```txt
created
updated
deleted
restored
published
unpublished
status_changed
feature_enabled
feature_disabled
exported
imported
downloaded
```

### 6.3 Actor

Każdy wpis historii musi wskazywać, kto wykonał akcję.

Actor może być:

```txt
tenant_user
identity
superadmin
system
job
api_token
license_server
```

Jeżeli akcję wykonał system/job, wpis nadal musi istnieć:

```txt
actor_type = system
actor_id = null
metadata_json = { "job": "RebuildPublishedPagesJob" }
```

### 6.4 Before / after

Przy edycji ważnych danych zapisujemy stan przed i po zmianie.

Nie logować plaintext danych wrażliwych, jeżeli naruszałoby to prywatność.

Dla danych wrażliwych:

- można logować nazwy pól, które się zmieniły,
- można logować masked value,
- można logować hash,
- nie logować pełnych sekretów/tokenów/notatek sensitive.

Przykład:

```json
{
  "changed_fields": ["email", "phone"],
  "sensitive": true
}
```

Zamiast:

```json
{
  "email": "real@email.com",
  "phone": "+48123456789"
}
```

### 6.5 Audit log techniczny

Oprócz historii per element system może mieć globalny audit log dla bezpieczeństwa.

Globalny audit log obejmuje:

- logowania,
- błędne logowania,
- zmiany 2FA,
- recovery codes,
- superadmin support access,
- eksporty,
- zmiany licencji,
- zmiany planów,
- działania krytyczne.

---

## 7. Wersjonowanie treści i edycji

### 7.1 CMS revisions

CMS musi mieć historię rewizji.

Dla stron/bloków CMS wymagane są:

- draft,
- published snapshot,
- revision history,
- informacja kto edytował,
- informacja kto opublikował,
- możliwość porównania zmian w przyszłości.

Przykład:

```txt
cms_pages
cms_page_revisions
published_pages
```

### 7.2 CRM change history

CRM również musi mieć historię zmian.

Dla kontaktów, firm, deali i notatek zapisujemy:

- kto utworzył,
- kto ostatnio edytował,
- kto zmienił status,
- kto usunął,
- kto przywrócił,
- co się zmieniło.

### 7.3 created_by / updated_by / deleted_by

Dla głównych tabel dodawać pola:

```txt
created_by
updated_by
deleted_by
```

Tam, gdzie ma to sens:

```php
$table->foreignId('created_by')->nullable()->index();
$table->foreignId('updated_by')->nullable()->index();
$table->foreignId('deleted_by')->nullable()->index();
```

Przy schema-per-tenant nie robić bezmyślnie cross-schema FK. Jeżeli actor jest z `public.identities`, trzymać logical reference bez FK albo użyć lokalnego tenant user id.

---

## 8. Multi-tenancy constraints

### 8.1 Tenant context

Każda operacja na danych tenantowych musi działać w poprawnym tenant context.

Nie wolno zakładać, że context istnieje “sam z siebie”.

Request:

```txt
resolve tenant -> initialize tenancy -> execute controller/action -> end tenancy
```

Job:

```txt
tenant_id in payload -> initialize tenancy -> handle job -> end tenancy
```

### 8.2 Brak danych tenantowych w public schema

Dane biznesowe klienta nie powinny trafiać do `public`.

`public` przechowuje:

- tenants,
- domains,
- identities,
- plans,
- subscriptions,
- features,
- licenses,
- system audit,
- dane globalne.

Tenant schema przechowuje:

- CMS,
- CRM,
- tenant users,
- files metadata,
- tenant audit/activity.

### 8.3 Brak tenant_id w głównych tabelach tenantowych

W tenant schema nie dodajemy `tenant_id` do każdej tabeli.

Izolacja wynika ze schemy.

Wyjątki muszą być uzasadnione.

---

## 9. Bezpieczeństwo i prywatność

### 9.1 Dane wrażliwe

Dane wrażliwe muszą być szyfrowane albo maskowane.

Dotyczy:

- tokenów,
- sekretów,
- OAuth refresh tokens,
- 2FA secrets,
- recovery codes,
- prywatnych danych kontaktowych,
- notatek oznaczonych jako sensitive,
- custom fields oznaczonych jako sensitive.

### 9.2 Nie logować sekretów

Nie wolno logować:

- haseł,
- tokenów,
- refresh tokenów,
- recovery codes,
- pełnych sekretów 2FA,
- pełnych danych płatniczych,
- danych wrażliwych bez maskowania.

### 9.3 Authorization

Każda akcja modyfikująca dane musi mieć authorization check:

- policy,
- gate,
- middleware,
- entitlement check,
- role/permission check.

Nie wystarczy ukryć przycisk w UI.

### 9.4 Public API

Publiczne CMS API:

- tylko read-only,
- tylko published content,
- brak CRM,
- brak danych użytkowników,
- brak draftów,
- rate limit,
- cache,
- CORS allow-list.

---

## 10. Entitlements, features, billing, license

### 10.1 Moduły nie pytają bezpośrednio o billing

Nie wolno w module CMS/CRM sprawdzać bezpośrednio Cashiera, Paddle, Stripe, subscription modelu albo license payload.

Źle:

```php
if ($tenant->subscription->active()) {
    // allow
}
```

Dobrze:

```php
if (! Entitlements::allows('crm.contacts')) {
    abort(403);
}
```

### 10.2 Jedna warstwa decyzyjna

Dostęp do funkcji i limitów przechodzi przez:

```php
Entitlements::allows('feature.key');
Entitlements::limit('limit.key');
Entitlements::config('feature.config');
```

Źródłem entitlementów może być:

- SaaS plan,
- Paddle subscription,
- ręczny override,
- self-hosted license,
- perpetual license,
- trial,
- internal/demo mode.

Moduły nie powinny wiedzieć, skąd pochodzi uprawnienie.

---

## 11. Komentarze w kodzie

Komentarze są wymagane przy bardziej skomplikowanym kodzie albo procesie.

Komentować należy:

- nietypowe decyzje biznesowe,
- edge case,
- obejścia ograniczeń frameworka/bazy,
- skomplikowane query,
- logikę security,
- logikę tenancy,
- logikę licencji,
- procesy wieloetapowe,
- fragmenty, które łatwo źle “uprościć”.

Nie komentować oczywistości.

Źle:

```php
// Increment counter by one
$count++;
```

Dobrze:

```php
// Tenant migrations must use the tenant schema as the first search_path entry.
 // Otherwise Laravel may create the migrations table in the public schema,
 // causing later tenants to skip already-recorded migrations.
$this->tenancy->initialize($tenant);
```

Komentarz ma wyjaśniać “dlaczego”, nie tylko “co”.

---

## 12. Transakcje

Operacje wieloetapowe, które zmieniają dane, powinny być transakcyjne.

Przykład:

```php
DB::transaction(function () use ($data) {
    $contact = $this->contacts->create($data);
    $this->activity->recordCreated($contact);
    event(new ContactCreated($contact));
});
```

Uwaga:

- nie wykonywać długich operacji zewnętrznych w transakcji,
- nie wykonywać requestów HTTP w transakcji,
- joby i eventy z efektami ubocznymi dispatchować po commicie, jeżeli to istotne.

---

## 13. Events i Jobs

### 13.1 Events

Events stosujemy do komunikacji między modułami, jeśli zmniejsza to sprzężenie.

Przykład:

```txt
ContactCreated
PagePublished
TenantFeatureEnabled
LicenseExpired
```

### 13.2 Jobs

Joby muszą zawierać `tenant_id`, jeśli działają na danych tenantowych.

Przykład:

```php
RebuildPublishedPageJob::dispatch($tenantId, $pageId);
```

Nie dispatchować jobów tenantowych bez tenant id.

### 13.3 Idempotencja

Joby powinny być idempotentne tam, gdzie to możliwe.

Retry joba nie może tworzyć duplikatów historii, rekordów albo wysyłek, jeśli da się tego uniknąć.

---

## 14. Walidacja

Każdy endpoint zapisujący dane powinien mieć FormRequest.

FormRequest odpowiada za:

- walidację danych wejściowych,
- proste reguły authorization, jeśli pasuje,
- normalizację prostych pól.

Nie wkładać dużej logiki biznesowej do FormRequest.

---

## 15. DTO

Dla bardziej złożonych danych wejściowych używać DTO.

Przykład:

```php
final readonly class ContactData
{
    public function __construct(
        public ?string $firstName,
        public ?string $lastName,
        public ?string $email,
        public ?string $phone,
    ) {}
}
```

DTO poprawia czytelność Action i testów.

Nie tworzyć DTO dla każdego prostego przypadku na siłę.

---

## 16. Query objects

Dla złożonych list, filtrów i sortowania stosować Query Object.

Przykład:

```txt
ContactIndexQuery
DealPipelineQuery
PublishedPageQuery
TenantSearchQuery
```

Kontroler nie powinien budować skomplikowanego query z wieloma warunkami.

---

## 17. API Resources

Nie zwracać modeli Eloquent bezpośrednio z API.

Używać:

- JsonResource,
- ResourceCollection,
- explicit arrays dla prostych wewnętrznych odpowiedzi.

Publiczne API musi mieć szczególnie ostrożnie kontrolowany output.

---

## 18. Nazewnictwo

Nazwy mają być jednoznaczne.

Preferować:

```txt
Tenant
Identity
TenantUser
Entitlement
Feature
License
Subscription
ActivityEntry
AuditLog
PublishedPage
PageRevision
```

Unikać nazw ogólnych:

```txt
Data
Manager
Helper
Thing
Item
Object
```

`Manager` tylko wtedy, gdy naprawdę zarządza procesem/systemem.

`Helper` prawie nigdy.

---

## 19. Błędy i wyjątki

Tworzyć domenowe wyjątki tam, gdzie poprawiają czytelność.

Przykład:

```txt
FeatureNotAvailableException
TenantNotResolvedException
LicenseExpiredException
CannotPublishDraftException
```

Nie łapać `Throwable` bez potrzeby.

Jeżeli wyjątek jest łapany, musi być jasny powód:

- retry,
- fallback,
- clean-up,
- user-friendly error,
- audit/security log.

---

## 20. Testy

Kod istotny biznesowo musi mieć testy.

Obowiązkowo testować:

- tenant isolation,
- feature/entitlement checks,
- soft delete/restore,
- activity history,
- audit logging,
- authorization,
- public API visibility,
- encryption/masking,
- license states,
- billing state mapping,
- CMS publishing,
- CRM data access.

### 20.1 Tenant isolation tests

Każdy moduł pracujący na danych tenantowych powinien mieć test, który potwierdza, że tenant A nie widzi danych tenant B.

### 20.2 History tests

Dla create/update/delete/restore/publish musi istnieć test, że wpis historii został utworzony.

### 20.3 Security tests

Dla akcji krytycznych testować brak uprawnień.

---

## 21. Frontend / Inertia

Frontend powinien być modularny zgodnie z backendem.

Przykład:

```txt
resources/js/Modules/
  Cms/
  Crm/
  Billing/
  Licensing/
  AdminConsole/
  TenantPanel/
```

Zasady:

- nie mieszać logiki CRM w CMS,
- nie powielać typów,
- używać TypeScript,
- nie polegać tylko na ukrywaniu elementów UI jako security,
- UI powinien odzwierciedlać Entitlements, ale backend musi zawsze weryfikować dostęp.

---

## 22. Struktura routes

Każdy moduł może mieć własne routes.

Przykład:

```txt
app/Modules/Crm/Routes/web.php
app/Modules/Crm/Routes/api.php
app/Modules/Cms/Routes/web.php
app/Modules/Cms/Routes/api.php
```

RouteServiceProvider albo ModuleServiceProvider ładuje routing modułów.

Routing powinien mieć middleware:

```txt
auth
tenant
verified
2fa
feature
permission/policy
```

---

## 23. Module Service Providers

Każdy większy moduł może mieć własny provider.

Przykład:

```txt
CrmServiceProvider
CmsServiceProvider
BillingServiceProvider
LicensingServiceProvider
```

Provider może rejestrować:

- routes,
- policies,
- bindings,
- event listeners,
- commands,
- migrations path, jeśli potrzebne.

---

## 24. Database migrations

Patrz osobny dokument:

```txt
migrations.md
```

Najważniejsze zasady:

- landlord migrations osobno,
- tenant migrations osobno,
- każda schema tenantowa ma własną tabelę `migrations`,
- `search_path` ustawiany centralnie,
- nie mieszać tenant i landlord migrations,
- nie robić cross-schema FK,
- nie dodawać `tenant_id` bez potrzeby do tabel tenantowych.

---

## 25. Data retention i usuwanie danych

Soft delete nie oznacza pełnego usunięcia danych.

Dla danych prywatnych musi istnieć docelowo polityka:

- retention,
- trwałego usuwania,
- eksportu danych,
- anonimizacji,
- usuwania na żądanie,
- crypto-shredding, jeśli per-tenant keys zostaną wdrożone.

Nie implementować przypadkowego hard delete bez przemyślenia skutków prawnych i biznesowych.

---

## 26. Import/export

Import i eksport muszą być audytowane.

Eksport danych prywatnych:

- wymaga uprawnienia,
- zapisuje audit log,
- powinien być wykonywany jako job przy większych zbiorach,
- powinien mieć expiration dla pliku eksportu,
- nie powinien być publiczny.

Import:

- powinien mieć dry-run/preview przy większych importach,
- powinien walidować dane,
- powinien zapisywać historię utworzenia/zmian,
- powinien być odporny na retry.

---

## 27. Cache

Cache musi uwzględniać tenant context.

Klucze cache powinny zawierać tenant id albo schema name.

Przykład:

```txt
tenant:{tenant_id}:cms:page:{path}
tenant:{tenant_id}:features
tenant:{tenant_id}:settings
```

Nie używać globalnych cache key dla danych tenantowych.

Publiczne CMS API powinno używać cache, ale cache musi być czyszczony po publikacji/odpublikowaniu.

---

## 28. Observability

Dodać sensowne logowanie techniczne, ale bez danych wrażliwych.

Logi powinny pomagać debugować:

- tenant resolving,
- license verification,
- failed jobs,
- failed payments/webhooks,
- failed public API requests,
- security events.

Logi nie zastępują audit/history entries.

---

## 29. Webhooki

Webhooki, np. Paddle, muszą być:

- weryfikowane podpisem,
- idempotentne,
- logowane technicznie,
- mapowane na wewnętrzne modele billing/license,
- nie mogą bezpośrednio nadawać dostępu w modułach biznesowych poza warstwą Entitlements.

Webhook payload można przechowywać, ale należy uważać na dane osobowe i wrażliwe.

---

## 30. Publiczne read API

Publiczne read API to osobny moduł.

Zasady:

- tylko published data,
- read-only,
- cache,
- rate limit,
- CORS allow-list,
- brak prywatnych danych,
- brak CRM,
- brak draftów,
- brak endpointów edycyjnych.

Endpointy publiczne nie mogą używać tego samego Resource co panel admina, jeśli panelowy Resource zawiera dane robocze/prywatne.

---

## 31. Admin Console vs Tenant Panel

Nie mieszać superadmina i użytkownika tenanta w jednym kontekście.

Superadmin:

- działa w Admin Console,
- zarządza tenantami,
- zarządza licencjami,
- zarządza features,
- może wejść w tryb support z audytem.

Tenant user:

- działa w Tenant Panel,
- zarządza danymi swojego tenanta,
- nie widzi globalnych danych systemowych.

---

## 32. Support access / impersonation

Support access musi być bezpieczny.

Wymagania:

- superadmin musi mieć 2FA,
- musi podać powód wejścia,
- sesja support ma expiration,
- wszystkie akcje są audytowane,
- UI pokazuje wyraźny banner trybu support,
- nie robić ukrytych backdoorów.

---

## 33. Dokumentacja kodu

Każdy większy moduł powinien mieć własny README albo sekcję dokumentacji:

```txt
app/Modules/Crm/README.md
app/Modules/Cms/README.md
app/Modules/Licensing/README.md
```

Dokumentować:

- odpowiedzialność modułu,
- główne modele,
- główne actions,
- eventy,
- ważne decyzje,
- ograniczenia.

---

## 34. Review checklist dla agenta

Przed zakończeniem zadania agent musi sprawdzić:

```txt
[ ] Czy kod działa w poprawnym tenant context?
[ ] Czy dane tenantowe nie trafiają do public schema?
[ ] Czy główne tabele mają soft deletes?
[ ] Czy akcje create/update/delete mają historię?
[ ] Czy actor jest zapisany w historii?
[ ] Czy są authorization checks?
[ ] Czy są entitlement checks, jeśli moduł jest płatny/feature-gated?
[ ] Czy dane wrażliwe nie są logowane?
[ ] Czy public API nie zwraca draftów/prywatnych danych?
[ ] Czy kontroler jest cienki?
[ ] Czy skomplikowana logika jest w Action/Service?
[ ] Czy kod jest DRY/KISS/YAGNI?
[ ] Czy skomplikowany proces ma komentarze?
[ ] Czy są testy dla istotnej logiki?
[ ] Czy joby tenantowe mają tenant_id?
[ ] Czy cache key zawiera tenant context?
[ ] Czy migracje są w poprawnym katalogu landlord/tenant?
```

---

## 35. Domyślny styl implementacji

Jeżeli nie ma innych instrukcji, wybieraj:

```txt
Controller + FormRequest + Action + Policy + Resource
```

Dla prostej operacji:

```txt
Controller + FormRequest + Model
```

Dla złożonej operacji:

```txt
Controller + FormRequest + DTO + Action + Service + Event + Job
```

Dla list z filtrami:

```txt
Controller + Request + QueryObject + ResourceCollection
```

Nie komplikować prostych CRUD-ów, ale nie wkładać procesów biznesowych do kontrolera.

---

## 36. Najważniejsza reguła

W Aegoryx każda zmiana danych biznesowych powinna odpowiadać na pytania:

```txt
Kto to zrobił?
Kiedy to zrobił?
Co zrobił?
Na jakim elemencie?
Co się zmieniło?
Z jakiego kontekstu/tenanta?
Czy miał do tego prawo?
Czy ta akcja powinna być widoczna w historii elementu?
```

Jeżeli kod nie pozwala odpowiedzieć na te pytania, jest niekompletny.

