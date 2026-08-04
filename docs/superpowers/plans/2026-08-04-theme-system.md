# Theme System Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a complete database-backed light/dark theme system with a global no-reload switcher and a fully tokenized Blade/Livewire UI.

**Architecture:** Store `light` or `dark` per authenticated admin identity and tenant user. Render the initial theme from the authenticated user when available, then let a small JavaScript controller update `data-theme`, `localStorage`, and the backend endpoint without reloading. Move view colors to semantic design-system tokens and components so light and dark mode are separately designed.

**Tech Stack:** Laravel, Blade, Livewire, Tailwind CSS v4, Vite, vanilla JavaScript, PHPUnit.

## Global Constraints

- Theme values are exactly `light` and `dark`.
- Persist preferences in `system.identities.theme` and tenant `users.theme`.
- Global switcher is required in both `admin.layout` and `tenant.layout`.
- Clicking the switcher must update the DOM immediately, write `localStorage`, and send a backend update without page reload.
- Logged-out fallback is `localStorage`; first-time fallback is `prefers-color-scheme`.
- Replace hard-coded dark-first Tailwind color classes with design-system classes, components, or CSS variables.
- Do not change unrelated business logic.
- Existing `.env.example` changes are unrelated and must not be reverted or committed as part of this work.

---

## File Structure

- Create `app/Support/Theme/ThemePreference.php`: shared enum for `light` and `dark`, validation values, and fallback parsing.
- Create `database/migrations/system/2026_08_04_000001_add_theme_to_identities_table.php`: adds `theme` to system identities.
- Create `database/migrations/tenant/2026_08_04_000001_add_theme_to_users_table.php`: adds `theme` to tenant users.
- Modify `app/Models/System/Identity.php`: property docblock, fillable, cast.
- Modify `app/Models/Tenant/User.php`: property docblock, fillable, cast, default creation behavior.
- Create `app/Http/Controllers/ThemePreferenceController.php`: small invokable/controller methods for current-user theme updates on both guards.
- Modify `app/Modules/AdminConsole/Routes/web.php`: add `PATCH /theme` named `admin.theme.update`.
- Modify `app/Modules/TenantPanel/Routes/web.php`: add `PATCH /panel/theme` named `tenant.theme.update`.
- Create `resources/views/components/theme/switcher.blade.php`: reusable global switcher.
- Modify `resources/js/app.js`: robust theme bootstrap and no-reload backend sync.
- Modify `resources/css/app.css`: expand token set and component classes.
- Modify `resources/views/admin/layout.blade.php`, `resources/views/tenant/layout.blade.php`, auth/public/error layouts: server-render initial theme for authenticated panel layouts, include the switcher in both panel headers, replace hard-coded shell colors.
- Modify dark-first Blade/Livewire views under `resources/views/admin`, `resources/views/tenant`, `resources/views/livewire/admin`, `resources/views/livewire/tenant`: migrate to `ui-*`, `x-ui.*`, and CSS variables.
- Modify `docs/design-system/README.md`: document the database-backed preference, switcher, token usage, and raw-color rule.
- Create `tests/Feature/Theme/ThemePreferenceTest.php`: endpoint and persistence tests across admin and tenant contexts.
- Modify `tests/Feature/TenantPanel/TenantProfileTest.php`: assert profile update still preserves theme and existing name/locale behavior.

---

### Task 1: Data Model And Enum

**Files:**
- Create: `app/Support/Theme/ThemePreference.php`
- Create: `database/migrations/system/2026_08_04_000001_add_theme_to_identities_table.php`
- Create: `database/migrations/tenant/2026_08_04_000001_add_theme_to_users_table.php`
- Modify: `app/Models/System/Identity.php`
- Modify: `app/Models/Tenant/User.php`
- Test: `tests/Feature/Theme/ThemePreferenceTest.php`

**Interfaces:**
- Produces: `App\Support\Theme\ThemePreference` enum with cases `Light = 'light'`, `Dark = 'dark'`, method `values(): array`, method `fallback(?string $value): self`.
- Produces: `Identity::$theme` and `User::$theme` cast to `ThemePreference`.
- Consumes: existing migration style under `database/migrations/system` and `database/migrations/tenant`.

- [ ] **Step 1: Write failing tests for migration defaults and casts**

Add `tests/Feature/Theme/ThemePreferenceTest.php`:

```php
<?php

namespace Tests\Feature\Theme;

use App\Models\System\Identity;
use App\Models\System\Tenant;
use App\Models\System\TenantDomain;
use App\Models\Tenant\User;
use App\Modules\Identity\Enums\IdentityStatus;
use App\Modules\Identity\Enums\TenantUserRole;
use App\Modules\Tenancy\Enums\TenantBillingModel;
use App\Modules\Tenancy\Enums\TenantDeploymentType;
use App\Modules\Tenancy\Enums\TenantDomainStatus;
use App\Modules\Tenancy\Enums\TenantDomainType;
use App\Modules\Tenancy\Enums\TenantLicenseType;
use App\Modules\Tenancy\Enums\TenantStatus;
use App\Support\Localization\Locale;
use App\Support\Theme\ThemePreference;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

final class ThemePreferenceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh', [
            '--database' => 'sqlite',
            '--path' => 'database/migrations/system',
        ]);

        Artisan::call('migrate', [
            '--database' => 'sqlite',
            '--path' => 'database/migrations/tenant',
        ]);
    }

    public function test_system_identity_theme_defaults_to_light_and_casts_to_enum(): void
    {
        $identity = Identity::query()->create([
            'email' => 'admin@example.test',
            'is_super_admin' => true,
            'status' => IdentityStatus::Active,
            'locale' => Locale::Polish,
        ]);

        $this->assertSame(ThemePreference::Light, $identity->refresh()->theme);

        $identity->forceFill(['theme' => ThemePreference::Dark])->save();

        $this->assertSame('dark', $identity->refresh()->getRawOriginal('theme'));
        $this->assertSame(ThemePreference::Dark, $identity->theme);
    }

    public function test_tenant_user_theme_defaults_to_light_and_casts_to_enum(): void
    {
        $this->domain($this->tenant());

        $user = User::query()->create([
            'name' => 'Member',
            'email' => 'member@example.test',
            'password' => 'secret-password',
            'role' => TenantUserRole::Member,
            'locale' => Locale::Polish,
        ]);

        $this->assertSame(ThemePreference::Light, $user->refresh()->theme);

        $user->forceFill(['theme' => ThemePreference::Dark])->save();

        $this->assertSame('dark', $user->refresh()->getRawOriginal('theme'));
        $this->assertSame(ThemePreference::Dark, $user->theme);
    }

    private function tenant(): Tenant
    {
        return Tenant::query()->create([
            'name' => 'Acme Tenant',
            'slug' => 'acme',
            'schema_name' => 'tenant_acme',
            'status' => TenantStatus::Active,
            'locale' => Locale::Polish,
            'deployment_type' => TenantDeploymentType::Saas,
            'billing_model' => TenantBillingModel::Subscription,
            'license_type' => TenantLicenseType::SaasSubscription,
        ]);
    }

    private function domain(Tenant $tenant): TenantDomain
    {
        return TenantDomain::query()->create([
            'tenant_id' => $tenant->id,
            'domain' => 'acme.aegoryx.test',
            'type' => TenantDomainType::Primary,
            'status' => TenantDomainStatus::Verified,
        ]);
    }
}
```

- [ ] **Step 2: Run tests and verify they fail**

Run:

```bash
php artisan test tests/Feature/Theme/ThemePreferenceTest.php
```

Expected: FAIL because `App\Support\Theme\ThemePreference` and the `theme` columns do not exist.

- [ ] **Step 3: Add the enum**

Create `app/Support/Theme/ThemePreference.php`:

```php
<?php

namespace App\Support\Theme;

enum ThemePreference: string
{
    case Light = 'light';
    case Dark = 'dark';

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $theme): string => $theme->value, self::cases());
    }

    public static function fallback(?string $value): self
    {
        return self::tryFrom((string) $value) ?? self::Light;
    }
}
```

- [ ] **Step 4: Add migrations**

Create `database/migrations/system/2026_08_04_000001_add_theme_to_identities_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('identities', function (Blueprint $table): void {
            $table->string('theme', 16)->default('light')->after('locale')->index();
        });
    }

    public function down(): void
    {
        Schema::table('identities', function (Blueprint $table): void {
            $table->dropColumn('theme');
        });
    }
};
```

Create `database/migrations/tenant/2026_08_04_000001_add_theme_to_users_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('theme', 16)->default('light')->after('locale')->index();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('theme');
        });
    }
};
```

- [ ] **Step 5: Update models**

In `app/Models/System/Identity.php`:

```php
use App\Support\Theme\ThemePreference;
```

Add `@property ThemePreference $theme` to the docblock. Add `'theme'` to `#[Fillable([...])]`. Add this cast:

```php
'theme' => ThemePreference::class,
```

In `app/Models/Tenant/User.php`:

```php
use App\Support\Theme\ThemePreference;
```

Add `@property ThemePreference $theme` to the docblock. Add `'theme'` to `#[Fillable([...])]`. Add this cast:

```php
'theme' => ThemePreference::class,
```

- [ ] **Step 6: Run tests and verify they pass**

Run:

```bash
php artisan test tests/Feature/Theme/ThemePreferenceTest.php
```

Expected: PASS for both data model tests.

- [ ] **Step 7: Commit**

Run:

```bash
git add app/Support/Theme/ThemePreference.php app/Models/System/Identity.php app/Models/Tenant/User.php database/migrations/system/2026_08_04_000001_add_theme_to_identities_table.php database/migrations/tenant/2026_08_04_000001_add_theme_to_users_table.php tests/Feature/Theme/ThemePreferenceTest.php
git commit -m "feat: add persisted theme preference"
```

---

### Task 2: Backend Theme Update Endpoints

**Files:**
- Create: `app/Http/Controllers/ThemePreferenceController.php`
- Modify: `app/Modules/AdminConsole/Routes/web.php`
- Modify: `app/Modules/TenantPanel/Routes/web.php`
- Test: `tests/Feature/Theme/ThemePreferenceTest.php`

**Interfaces:**
- Consumes: `ThemePreference::values()` from Task 1.
- Produces: routes `admin.theme.update` and `tenant.theme.update`.
- Produces: JSON response `['theme' => 'light'|'dark']`.

- [ ] **Step 1: Add failing endpoint tests**

Append these methods to `tests/Feature/Theme/ThemePreferenceTest.php`:

```php
public function test_admin_can_update_own_theme_preference(): void
{
    $identity = Identity::query()->create([
        'email' => 'admin@example.test',
        'is_super_admin' => true,
        'status' => IdentityStatus::Active,
        'locale' => Locale::Polish,
    ]);

    $this->actingAs($identity, 'admin');

    $this
        ->patchJson('http://admin.aegoryx.test/theme', ['theme' => ThemePreference::Dark->value])
        ->assertOk()
        ->assertJson(['theme' => ThemePreference::Dark->value]);

    $this->assertSame(ThemePreference::Dark, $identity->refresh()->theme);
}

public function test_tenant_user_can_update_own_theme_preference(): void
{
    $this->domain($this->tenant());

    $user = User::query()->create([
        'name' => 'Member',
        'email' => 'member@example.test',
        'password' => 'secret-password',
        'role' => TenantUserRole::Member,
        'locale' => Locale::Polish,
    ]);

    $this->actingAs($user, 'web');

    $this
        ->patchJson('http://acme.aegoryx.test/panel/theme', ['theme' => ThemePreference::Dark->value])
        ->assertOk()
        ->assertJson(['theme' => ThemePreference::Dark->value]);

    $this->assertSame(ThemePreference::Dark, $user->refresh()->theme);
}

public function test_invalid_theme_preference_is_rejected(): void
{
    $identity = Identity::query()->create([
        'email' => 'admin@example.test',
        'is_super_admin' => true,
        'status' => IdentityStatus::Active,
        'locale' => Locale::Polish,
    ]);

    $this->actingAs($identity, 'admin');

    $this
        ->patchJson('http://admin.aegoryx.test/theme', ['theme' => 'system'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('theme');

    $this->assertSame(ThemePreference::Light, $identity->refresh()->theme);
}

public function test_guest_cannot_update_theme_preference(): void
{
    $this
        ->patchJson('http://admin.aegoryx.test/theme', ['theme' => ThemePreference::Dark->value])
        ->assertUnauthorized();

    $this->domain($this->tenant());

    $this
        ->patchJson('http://acme.aegoryx.test/panel/theme', ['theme' => ThemePreference::Dark->value])
        ->assertUnauthorized();
}
```

- [ ] **Step 2: Run tests and verify they fail**

Run:

```bash
php artisan test tests/Feature/Theme/ThemePreferenceTest.php
```

Expected: FAIL with 404 for the new endpoint tests.

- [ ] **Step 3: Implement controller**

Create `app/Http/Controllers/ThemePreferenceController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\System\Identity;
use App\Models\Tenant\User;
use App\Support\Theme\ThemePreference;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;

final class ThemePreferenceController extends Controller
{
    public function admin(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'theme' => ['required', 'string', Rule::in(ThemePreference::values())],
        ]);

        /** @var Identity $user */
        $user = $request->user('admin');
        $theme = ThemePreference::from($validated['theme']);

        $user->forceFill(['theme' => $theme])->save();

        return response()->json(['theme' => $theme->value]);
    }

    public function tenant(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'theme' => ['required', 'string', Rule::in(ThemePreference::values())],
        ]);

        /** @var User $user */
        $user = $request->user('web');
        $theme = ThemePreference::from($validated['theme']);

        $user->forceFill([
            'theme' => $theme,
            'updated_by' => $user->id,
        ])->save();

        return response()->json(['theme' => $theme->value]);
    }
}
```

- [ ] **Step 4: Add routes**

In `app/Modules/AdminConsole/Routes/web.php`, add:

```php
use App\Http\Controllers\ThemePreferenceController;
```

Inside the authenticated admin route group:

```php
Route::patch('/theme', [ThemePreferenceController::class, 'admin'])->name('theme.update');
```

In `app/Modules/TenantPanel/Routes/web.php`, add:

```php
use App\Http\Controllers\ThemePreferenceController;
```

Inside `Route::prefix('panel')->group(...)`:

```php
Route::patch('theme', [ThemePreferenceController::class, 'tenant'])->name('theme.update');
```

- [ ] **Step 5: Run tests and verify they pass**

Run:

```bash
php artisan test tests/Feature/Theme/ThemePreferenceTest.php
```

Expected: PASS.

- [ ] **Step 6: Commit**

Run:

```bash
git add app/Http/Controllers/ThemePreferenceController.php app/Modules/AdminConsole/Routes/web.php app/Modules/TenantPanel/Routes/web.php tests/Feature/Theme/ThemePreferenceTest.php
git commit -m "feat: add theme preference endpoints"
```

---

### Task 3: Theme Bootstrap And Global Switcher

**Files:**
- Create: `resources/views/components/theme/switcher.blade.php`
- Modify: `resources/js/app.js`
- Modify: `resources/views/admin/layout.blade.php`
- Modify: `resources/views/tenant/layout.blade.php`
- Modify: `resources/views/admin/auth/login.blade.php`
- Modify: `resources/views/admin/auth/request-password-reset.blade.php`
- Modify: `resources/views/admin/auth/reset-password.blade.php`
- Modify: `resources/views/admin/auth/two-factor-challenge.blade.php`
- Modify: `resources/views/tenant/auth/login.blade.php`
- Modify: `resources/views/tenant/auth/request-password-reset.blade.php`
- Modify: `resources/views/tenant/auth/reset-password.blade.php`
- Modify: `resources/views/welcome.blade.php`
- Modify: `resources/views/errors/403.blade.php`
- Test: `tests/Feature/Theme/ThemePreferenceTest.php`

**Interfaces:**
- Consumes: routes `admin.theme.update` and `tenant.theme.update`.
- Produces: Blade component props `theme`, `action`, `align`.
- Produces: JavaScript global `window.aegoryxTheme.set(theme, options = {})`.

- [ ] **Step 1: Add failing render tests**

Append these methods to `tests/Feature/Theme/ThemePreferenceTest.php`:

```php
public function test_admin_layout_renders_saved_theme_and_switcher_endpoint(): void
{
    $identity = Identity::query()->create([
        'email' => 'admin@example.test',
        'is_super_admin' => true,
        'status' => IdentityStatus::Active,
        'locale' => Locale::Polish,
        'theme' => ThemePreference::Dark,
    ]);

    $this->actingAs($identity, 'admin');

    $this
        ->get('http://admin.aegoryx.test/')
        ->assertOk()
        ->assertSee('data-theme="dark"', false)
        ->assertSee('data-theme-switcher', false)
        ->assertSee('data-theme-endpoint="http://admin.aegoryx.test/theme"', false);
}

public function test_tenant_layout_renders_saved_theme_and_switcher_endpoint(): void
{
    $this->domain($this->tenant());

    $user = User::query()->create([
        'name' => 'Member',
        'email' => 'member@example.test',
        'password' => 'secret-password',
        'role' => TenantUserRole::Member,
        'locale' => Locale::Polish,
        'theme' => ThemePreference::Dark,
    ]);

    $this->actingAs($user, 'web');

    $this
        ->get('http://acme.aegoryx.test/panel')
        ->assertOk()
        ->assertSee('data-theme="dark"', false)
        ->assertSee('data-theme-switcher', false)
        ->assertSee('data-theme-endpoint="http://acme.aegoryx.test/panel/theme"', false);
}
```

- [ ] **Step 2: Run tests and verify they fail**

Run:

```bash
php artisan test tests/Feature/Theme/ThemePreferenceTest.php
```

Expected: FAIL because layouts do not render the saved `data-theme` or switcher.

- [ ] **Step 3: Update server-rendered `html` tags**

In authenticated layouts, compute and render the current user theme:

```blade
@php
    $currentTheme = auth('admin')->user()?->theme?->value ?? 'light';
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ $currentTheme }}">
```

Use `auth('web')` in `resources/views/tenant/layout.blade.php`.

For logged-out/public/error screens, keep no hard-coded `data-theme` unless a shared guest layout is introduced. The JS bootstrap will apply `localStorage` or `prefers-color-scheme`.

- [ ] **Step 4: Create the switcher component**

Create `resources/views/components/theme/switcher.blade.php`:

```blade
@props([
    'theme' => 'light',
    'action' => null,
    'align' => 'end',
])

@php
    $theme = in_array($theme, ['light', 'dark'], true) ? $theme : 'light';
@endphp

<div
    data-theme-switcher
    @if ($action) data-theme-endpoint="{{ $action }}" @endif
    data-current-theme="{{ $theme }}"
    class="ui-theme-switcher"
    role="group"
    aria-label="{{ __('common.theme') }}"
>
    <button
        type="button"
        class="ui-theme-option"
        data-theme-value="light"
        aria-pressed="{{ $theme === 'light' ? 'true' : 'false' }}"
        title="{{ __('common.theme_light') }}"
    >
        <span aria-hidden="true">L</span>
        <span class="sr-only">{{ __('common.theme_light') }}</span>
    </button>
    <button
        type="button"
        class="ui-theme-option"
        data-theme-value="dark"
        aria-pressed="{{ $theme === 'dark' ? 'true' : 'false' }}"
        title="{{ __('common.theme_dark') }}"
    >
        <span aria-hidden="true">D</span>
        <span class="sr-only">{{ __('common.theme_dark') }}</span>
    </button>
</div>
```

Add translations for `common.theme`, `common.theme_light`, and `common.theme_dark` in every locale file under `lang/*/common.php`. Use translated equivalents where practical; English fallback terms are acceptable only if the locale file already mixes untranslated technical UI copy.

- [ ] **Step 5: Insert switchers in panel headers**

In `resources/views/admin/layout.blade.php`, add the switcher next to logout:

```blade
<div class="flex items-center gap-3">
    <x-theme.switcher :theme="$currentTheme" :action="route('admin.theme.update')" />
    <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <x-ui.button type="submit" variant="secondary" size="sm">
            {{ __('admin.sign_out') }}
        </x-ui.button>
    </form>
</div>
```

In `resources/views/tenant/layout.blade.php`, add:

```blade
<x-theme.switcher :theme="$currentTheme" :action="route('tenant.theme.update')" />
```

Place it in the existing header action row before the user summary or logout button.

- [ ] **Step 6: Refactor JavaScript**

Replace `resources/js/app.js` with:

```js
const THEME_KEY = 'aegoryx.theme';
const THEMES = ['light', 'dark'];

function validTheme(theme) {
    return THEMES.includes(theme);
}

function fallbackTheme() {
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
}

function storedTheme() {
    const theme = localStorage.getItem(THEME_KEY);

    return validTheme(theme) ? theme : null;
}

function currentTheme() {
    const htmlTheme = document.documentElement.dataset.theme;

    return validTheme(htmlTheme) ? htmlTheme : storedTheme() ?? fallbackTheme();
}

function applyTheme(theme) {
    const nextTheme = validTheme(theme) ? theme : fallbackTheme();

    document.documentElement.dataset.theme = nextTheme;
    localStorage.setItem(THEME_KEY, nextTheme);
    document.querySelectorAll('[data-theme-switcher]').forEach((switcher) => {
        switcher.dataset.currentTheme = nextTheme;
        switcher.querySelectorAll('[data-theme-value]').forEach((button) => {
            button.setAttribute('aria-pressed', button.dataset.themeValue === nextTheme ? 'true' : 'false');
        });
    });

    return nextTheme;
}

async function persistTheme(endpoint, theme) {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const response = await fetch(endpoint, {
        method: 'PATCH',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            ...(token ? { 'X-CSRF-TOKEN': token } : {}),
        },
        body: JSON.stringify({ theme }),
    });

    if (!response.ok) {
        throw new Error(`Theme update failed with ${response.status}`);
    }

    return response.json();
}

applyTheme(currentTheme());

document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-theme-value]');

    if (!button) {
        return;
    }

    const switcher = button.closest('[data-theme-switcher]');
    const theme = button.dataset.themeValue;

    if (!switcher || !validTheme(theme)) {
        return;
    }

    const nextTheme = applyTheme(theme);
    switcher.dataset.themeStatus = 'saving';

    const endpoint = switcher.dataset.themeEndpoint;

    if (!endpoint) {
        switcher.dataset.themeStatus = 'saved';
        return;
    }

    persistTheme(endpoint, nextTheme)
        .then(() => {
            switcher.dataset.themeStatus = 'saved';
        })
        .catch(() => {
            switcher.dataset.themeStatus = 'error';
        });
});

window.aegoryxTheme = {
    set(theme) {
        return applyTheme(theme);
    },
    clear() {
        localStorage.removeItem(THEME_KEY);
        return applyTheme(fallbackTheme());
    },
};
```

Ensure all base layouts include:

```blade
<meta name="csrf-token" content="{{ csrf_token() }}">
```

- [ ] **Step 7: Run tests and build**

Run:

```bash
php artisan test tests/Feature/Theme/ThemePreferenceTest.php
npm run build
```

Expected: PASS and successful Vite build.

- [ ] **Step 8: Commit**

Run:

```bash
git add resources/js/app.js resources/views/components/theme/switcher.blade.php resources/views/admin/layout.blade.php resources/views/tenant/layout.blade.php resources/views/admin/auth resources/views/tenant/auth resources/views/welcome.blade.php resources/views/errors/403.blade.php lang tests/Feature/Theme/ThemePreferenceTest.php
git commit -m "feat: add global theme switcher"
```

---

### Task 4: Design Tokens And Reusable UI Primitives

**Files:**
- Modify: `resources/css/app.css`
- Modify: `resources/views/components/ui/badge.blade.php`
- Modify: `resources/views/components/ui/button.blade.php`
- Create: `resources/views/components/ui/alert.blade.php`
- Test: no PHP test required; verified through build and view audit.

**Interfaces:**
- Produces CSS classes: `ui-shell-border`, `ui-header`, `ui-sidebar`, `ui-nav-link`, `ui-nav-link-active`, `ui-nav-pill`, `ui-nav-pill-active`, `ui-alert`, `ui-alert-success`, `ui-alert-info`, `ui-alert-warning`, `ui-alert-danger`, `ui-theme-switcher`, `ui-theme-option`, `ui-prose`, `ui-code-block`.
- Consumes existing classes: `ds-app`, `ds-shell`, `ui-card`, `ui-muted-panel`, `ui-table`, `ui-btn-*`, `ui-badge-*`.

- [ ] **Step 1: Expand CSS tokens**

In `resources/css/app.css`, replace the duplicate `:root`, media dark, and `[data-theme='dark']` blocks with explicit light and dark token sets:

```css
:root,
[data-theme='light'] {
    color-scheme: light;
    --ui-bg: #f7f8fb;
    --ui-surface: #ffffff;
    --ui-surface-muted: #f1f5f9;
    --ui-surface-inset: #eef2f7;
    --ui-surface-raised: #ffffff;
    --ui-sidebar: #ffffff;
    --ui-header: #ffffff;
    --ui-border: #d8e0ea;
    --ui-border-subtle: #e8edf3;
    --ui-border-strong: #aebccc;
    --ui-text: #101827;
    --ui-text-muted: #526071;
    --ui-text-subtle: #768294;
    --ui-nav-text: #526071;
    --ui-nav-hover-bg: #eef6fb;
    --ui-nav-hover-text: #0f172a;
    --ui-nav-active-bg: #0284c7;
    --ui-nav-active-text: #ffffff;
    --ui-accent: #0284c7;
    --ui-accent-hover: #0369a1;
    --ui-accent-soft: #dff3ff;
    --ui-accent-text: #075985;
    --ui-success: #047857;
    --ui-success-soft: #dff8ec;
    --ui-warning: #b45309;
    --ui-warning-soft: #fff1cf;
    --ui-danger: #c0262d;
    --ui-danger-soft: #ffe3e5;
    --ui-info: #2563eb;
    --ui-info-soft: #dbeafe;
    --ui-focus: #38bdf8;
    --ui-shadow: 0 1px 2px rgb(15 23 42 / 0.08);
}

[data-theme='dark'] {
    color-scheme: dark;
    --ui-bg: #09090b;
    --ui-surface: #18181b;
    --ui-surface-muted: #111113;
    --ui-surface-inset: #0f0f12;
    --ui-surface-raised: #202024;
    --ui-sidebar: #101014;
    --ui-header: #111113;
    --ui-border: #2f3037;
    --ui-border-subtle: #24252b;
    --ui-border-strong: #52525b;
    --ui-text: #f4f4f5;
    --ui-text-muted: #a1a1aa;
    --ui-text-subtle: #71717a;
    --ui-nav-text: #d4d4d8;
    --ui-nav-hover-bg: #202024;
    --ui-nav-hover-text: #ffffff;
    --ui-nav-active-bg: #0ea5e9;
    --ui-nav-active-text: #ffffff;
    --ui-accent: #0ea5e9;
    --ui-accent-hover: #38bdf8;
    --ui-accent-soft: #082f49;
    --ui-accent-text: #bae6fd;
    --ui-success: #34d399;
    --ui-success-soft: #052e1b;
    --ui-warning: #f59e0b;
    --ui-warning-soft: #451a03;
    --ui-danger: #f87171;
    --ui-danger-soft: #450a0a;
    --ui-info: #60a5fa;
    --ui-info-soft: #172554;
    --ui-focus: #7dd3fc;
    --ui-shadow: 0 1px 2px rgb(0 0 0 / 0.35);
}
```

- [ ] **Step 2: Add reusable component classes**

Add component CSS in `@layer components`:

```css
.ui-shell-border { border-color: var(--ui-border); }
.ui-header { background: var(--ui-header); border-color: var(--ui-border); }
.ui-sidebar { background: var(--ui-sidebar); border-color: var(--ui-border); }
.ui-nav-link { color: var(--ui-nav-text); }
.ui-nav-link:hover { background: var(--ui-nav-hover-bg); color: var(--ui-nav-hover-text); }
.ui-nav-link-active { background: var(--ui-nav-active-bg); color: var(--ui-nav-active-text); }
.ui-nav-pill { background: var(--ui-surface-muted); color: var(--ui-nav-text); }
.ui-nav-pill-active { background: var(--ui-nav-active-bg); color: var(--ui-nav-active-text); }
.ui-alert { border: 1px solid transparent; border-radius: 0.375rem; padding: 0.75rem 1rem; font-size: 0.875rem; line-height: 1.25rem; }
.ui-alert-success { border-color: var(--ui-success); background: var(--ui-success-soft); color: var(--ui-success); }
.ui-alert-info { border-color: var(--ui-info); background: var(--ui-info-soft); color: var(--ui-info); }
.ui-alert-warning { border-color: var(--ui-warning); background: var(--ui-warning-soft); color: var(--ui-warning); }
.ui-alert-danger { border-color: var(--ui-danger); background: var(--ui-danger-soft); color: var(--ui-danger); }
.ui-code-block { overflow-x: auto; border: 1px solid var(--ui-border); border-radius: 0.375rem; background: var(--ui-surface-inset); padding: 1rem; color: var(--ui-text); }
.ui-prose { color: var(--ui-text-muted); }
.ui-prose :where(h1, h2, h3, strong) { color: var(--ui-text); }
.ui-theme-switcher { display: inline-flex; align-items: center; gap: 0.125rem; border: 1px solid var(--ui-border); border-radius: 0.375rem; background: var(--ui-surface-muted); padding: 0.125rem; }
.ui-theme-option { display: inline-flex; min-width: 2rem; min-height: 2rem; align-items: center; justify-content: center; border-radius: 0.25rem; color: var(--ui-text-muted); font-size: 0.75rem; font-weight: 700; }
.ui-theme-option:hover { color: var(--ui-text); }
.ui-theme-option[aria-pressed='true'] { background: var(--ui-surface); color: var(--ui-text); box-shadow: var(--ui-shadow); }
.ui-theme-switcher[data-theme-status='error'] { border-color: var(--ui-danger); }
```

Adjust exact formatting to match surrounding CSS.

- [ ] **Step 3: Create alert component**

Create `resources/views/components/ui/alert.blade.php`:

```blade
@props([
    'variant' => 'info',
])

@php
    $variantClass = match ($variant) {
        'success' => 'ui-alert-success',
        'warning' => 'ui-alert-warning',
        'danger' => 'ui-alert-danger',
        default => 'ui-alert-info',
    };
@endphp

<div {{ $attributes->class("ui-alert {$variantClass}") }}>
    {{ $slot }}
</div>
```

- [ ] **Step 4: Build assets**

Run:

```bash
npm run build
```

Expected: successful build.

- [ ] **Step 5: Commit**

Run:

```bash
git add resources/css/app.css resources/views/components/ui/alert.blade.php resources/views/components/ui/badge.blade.php resources/views/components/ui/button.blade.php
git commit -m "feat: expand theme design tokens"
```

---

### Task 5: Migrate Shells, Auth, Public, And Admin Views

**Files:**
- Modify: `resources/views/admin/layout.blade.php`
- Modify: `resources/views/admin/auth/*.blade.php`
- Modify: `resources/views/livewire/admin/auth/*.blade.php`
- Modify: `resources/views/admin/dashboard.blade.php`
- Modify: `resources/views/admin/section.blade.php`
- Modify: `resources/views/admin/tenants/*.blade.php`
- Modify: `resources/views/livewire/admin/tenants/*.blade.php`
- Modify: `resources/views/admin/licenses/*.blade.php`
- Modify: `resources/views/livewire/admin/licenses/*.blade.php`
- Modify: `resources/views/admin/billing/*.blade.php`
- Modify: `resources/views/admin/audit/index.blade.php`
- Modify: `resources/views/admin/support/index.blade.php`
- Modify: `resources/views/livewire/admin/support/index.blade.php`
- Modify: `resources/views/admin/security/index.blade.php`
- Modify: `resources/views/livewire/admin/security/two-factor-settings.blade.php`
- Modify: `resources/views/welcome.blade.php`
- Modify: `resources/views/errors/403.blade.php`
- Test: existing admin feature tests.

**Interfaces:**
- Consumes CSS classes from Task 4.
- Produces admin/public screens without hard-coded dark-first color utilities.

- [ ] **Step 1: Run current admin tests**

Run:

```bash
php artisan test tests/Feature/AdminConsole tests/Feature/Identity/SuperadminTwoFactorLoginTest.php
```

Expected: PASS before view migration.

- [ ] **Step 2: Migrate admin layout shell**

Replace hard-coded shell colors:

```blade
border-neutral-800 -> ui-shell-border
text-neutral-500 -> ui-caption
text-neutral-400 -> ui-body
text-neutral-300 -> ui-nav-link
hover:bg-neutral-900 hover:text-white -> ui-nav-link
bg-sky-500 text-white -> ui-nav-link-active
bg-neutral-900 text-neutral-300 -> ui-nav-pill
```

Use `ui-sidebar` on the sidebar and `ui-header` on the header.

- [ ] **Step 3: Migrate admin cards, sections, and alerts**

Use these replacements across admin views:

```blade
<section class="rounded border border-neutral-800 bg-neutral-900 p-5">
```

becomes:

```blade
<section class="ui-card p-5">
```

Dark success alerts become:

```blade
<x-ui.alert variant="success" class="mb-5">
    {{ session('success') }}
</x-ui.alert>
```

Dark danger alerts become:

```blade
<x-ui.alert variant="danger" class="mb-5">
    {{ $message }}
</x-ui.alert>
```

Links such as `text-sky-300 hover:text-sky-200` become `ui-link`.

- [ ] **Step 4: Migrate admin tables**

For tables using raw Tailwind:

```blade
<table class="min-w-full divide-y divide-neutral-800 text-sm">
```

becomes:

```blade
<table class="ui-table">
```

Remove raw `thead`/`tbody` background and divide classes unless needed for layout. Replace `text-neutral-*` cells with `text-[var(--ui-text)]`, `text-[var(--ui-text-muted)]`, or `ui-caption`.

- [ ] **Step 5: Migrate auth/public/error screens**

Use `ds-app`, `ui-card`, `ui-heading-*`, `ui-body`, `ui-caption`, `ui-link`, `x-ui.button`, and `x-ui.alert`. Remove `bg-neutral-950`, `bg-neutral-900`, `text-neutral-100`, and `text-neutral-400` from welcome and 403.

- [ ] **Step 6: Audit admin/public view colors**

Run:

```bash
rg -n "\\b(bg|text|border|divide)-(neutral|slate|zinc|gray|stone|red|emerald|amber|sky)-" resources/views/admin resources/views/livewire/admin resources/views/welcome.blade.php resources/views/errors/403.blade.php
```

Expected: no matches except intentional fixed accent text such as `text-white` is not included in this audit pattern.

- [ ] **Step 7: Run tests and build**

Run:

```bash
php artisan test tests/Feature/AdminConsole tests/Feature/Identity/SuperadminTwoFactorLoginTest.php tests/Feature/Theme/ThemePreferenceTest.php
npm run build
```

Expected: PASS and successful build.

- [ ] **Step 8: Commit**

Run:

```bash
git add resources/views/admin resources/views/livewire/admin resources/views/welcome.blade.php resources/views/errors/403.blade.php
git commit -m "refactor: migrate admin views to theme tokens"
```

---

### Task 6: Migrate Tenant Views

**Files:**
- Modify: `resources/views/tenant/layout.blade.php`
- Modify: `resources/views/tenant/auth/*.blade.php`
- Modify: `resources/views/livewire/tenant/auth/*.blade.php`
- Modify: `resources/views/tenant/dashboard.blade.php`
- Modify: `resources/views/tenant/modules/placeholder.blade.php`
- Modify: `resources/views/tenant/profile/edit.blade.php`
- Modify: `resources/views/tenant/settings/index.blade.php`
- Modify: `resources/views/tenant/users/index.blade.php`
- Modify: `resources/views/livewire/tenant/users/index.blade.php`
- Modify: `resources/views/tenant/cms/pages/index.blade.php`
- Modify: `resources/views/livewire/tenant/cms/pages/index.blade.php`
- Modify: `resources/views/tenant/crm/**/*.blade.php`
- Modify: `resources/views/tenant/files/*.blade.php`
- Modify: `resources/views/tenant/activity/*.blade.php`
- Modify: `resources/views/tenant/security/index.blade.php`
- Modify: `resources/views/livewire/tenant/security/two-factor-settings.blade.php`
- Test: existing tenant, CRM, CMS, files, activity, and theme tests.

**Interfaces:**
- Consumes CSS classes from Task 4.
- Produces tenant screens without hard-coded dark-first color utilities.

- [ ] **Step 1: Run current tenant-facing tests**

Run:

```bash
php artisan test tests/Feature/TenantPanel tests/Feature/Crm tests/Feature/Cms tests/Feature/Files tests/Feature/Theme/ThemePreferenceTest.php
```

Expected: PASS before migration.

- [ ] **Step 2: Migrate tenant layout shell**

Apply the same shell replacements as admin:

```blade
ui-sidebar
ui-header
ui-nav-link
ui-nav-link-active
ui-nav-pill
ui-nav-pill-active
ui-muted-panel
ui-caption
ui-body
```

Tenant identity and active-tenant summary panels should use `ui-muted-panel` and text tokens.

- [ ] **Step 3: Migrate tenant dashboard and modules**

Replace dark raw panels with `ui-card` and nested non-card areas with `ui-muted-panel`. Module tiles should use:

```blade
class="ui-muted-panel block p-4 hover:border-[var(--ui-border-strong)]"
```

Disabled tiles retain opacity but use token text.

- [ ] **Step 4: Migrate profile, settings, users, security**

Replace success/info alerts with `x-ui.alert`. Replace read-only text classes like `text-neutral-500` with `ui-body` or `ui-caption`. Ensure existing form components keep `ui-input`, `ui-select`, and `ui-label`.

- [ ] **Step 5: Migrate CMS and preview prose**

Replace preview prose wrapper:

```blade
prose prose-invert max-w-none
```

with:

```blade
ui-prose max-w-none
```

Replace preview body `text-neutral-300` with `text-[var(--ui-text-muted)]`.

- [ ] **Step 6: Migrate CRM lists and edit views**

For CRM contacts, companies, deals, notes, and tasks:

```blade
rounded border border-neutral-800 bg-neutral-900
```

becomes `ui-card`; raw tables become `ui-table`; raw cell text becomes token text. Delete actions keep `text-[var(--ui-danger)]`.

- [ ] **Step 7: Migrate files and activity detail code blocks**

Replace `pre` blocks with `ui-code-block`. Replace file/activity metadata labels and values with `ui-label`, `ui-caption`, and `text-[var(--ui-text)]`.

- [ ] **Step 8: Audit tenant view colors**

Run:

```bash
rg -n "\\b(bg|text|border|divide)-(neutral|slate|zinc|gray|stone|red|emerald|amber|sky)-" resources/views/tenant resources/views/livewire/tenant
```

Expected: no matches except intentional fixed accent text such as `text-white` is not included in this audit pattern.

- [ ] **Step 9: Run tests and build**

Run:

```bash
php artisan test tests/Feature/TenantPanel tests/Feature/Crm tests/Feature/Cms tests/Feature/Files tests/Feature/Theme/ThemePreferenceTest.php
npm run build
```

Expected: PASS and successful build.

- [ ] **Step 10: Commit**

Run:

```bash
git add resources/views/tenant resources/views/livewire/tenant
git commit -m "refactor: migrate tenant views to theme tokens"
```

---

### Task 7: Profile Regression, Docs, And Final Verification

**Files:**
- Modify: `tests/Feature/TenantPanel/TenantProfileTest.php`
- Modify: `docs/design-system/README.md`
- Test: all theme-relevant tests and audits.

**Interfaces:**
- Consumes `ThemePreference` enum and profile behavior from earlier tasks.
- Produces updated docs for future contributors.

- [ ] **Step 1: Extend tenant profile regression test**

In `tests/Feature/TenantPanel/TenantProfileTest.php`, import:

```php
use App\Support\Theme\ThemePreference;
```

In `test_user_can_update_own_profile_locale`, create the user with a dark theme:

```php
'theme' => ThemePreference::Dark,
```

After refresh, assert:

```php
$this->assertSame(ThemePreference::Dark, $user->theme);
```

This confirms the profile form does not accidentally overwrite theme while saving name and locale.

- [ ] **Step 2: Update design-system docs**

Update `docs/design-system/README.md` Motywy section to state:

```markdown
Motyw działa przez `data-theme` na elemencie `html`. Po zalogowaniu źródłem prawdy jest preferencja użytkownika w bazie: `identities.theme` dla panelu admina i `users.theme` dla panelu tenantowego. Globalny przełącznik zapisuje wybór bez przeładowania strony: najpierw zmienia `data-theme`, potem zapisuje `localStorage` pod `aegoryx.theme`, a następnie wysyła `PATCH` do endpointu preferencji.

Dla stron niezalogowanych fallback to `localStorage`, a przy pierwszej wizycie preferencja przeglądarki `prefers-color-scheme`.
```

Keep the existing guidance that new views must use semantic token classes and variables instead of raw palette utilities.

- [ ] **Step 3: Run full targeted verification**

Run:

```bash
php artisan test tests/Feature/Theme/ThemePreferenceTest.php tests/Feature/TenantPanel/TenantProfileTest.php tests/Feature/AdminConsole tests/Feature/TenantPanel tests/Feature/Crm tests/Feature/Cms tests/Feature/Files
npm run build
rg -n "\\b(bg|text|border|divide)-(neutral|slate|zinc|gray|stone|red|emerald|amber|sky)-" resources/views
```

Expected:

- PHPUnit passes.
- Vite build succeeds.
- `rg` reports no dark-first raw color matches in `resources/views`, except any explicitly reviewed and documented intentional fixed colors.

- [ ] **Step 4: Optional browser verification**

If the local app can run, start the dev server and inspect:

```bash
npm run dev
php artisan serve
```

Check representative pages in light and dark:

- admin dashboard
- admin tenants list
- admin support Livewire page
- tenant dashboard
- tenant CMS page
- tenant CRM contacts
- tenant settings/profile

Expected: no unreadable text, no dark panels in light mode, no light panels in dark mode, and the switcher changes theme without navigation.

- [ ] **Step 5: Commit**

Run:

```bash
git add tests/Feature/TenantPanel/TenantProfileTest.php docs/design-system/README.md
git commit -m "docs: document theme preference system"
```

---

## Self-Review

- Spec coverage: data model, server rendering, localStorage/system fallback, no-reload switcher, backend endpoints, design tokens, view migration, error handling, tests, docs, and verification are covered by Tasks 1-7.
- Red-flag scan: no incomplete markers, deferred implementation notes, or unresolved route alternatives remain.
- Type consistency: `ThemePreference`, route names, endpoint paths, `theme` property names, and JavaScript selectors are consistent across tasks.
