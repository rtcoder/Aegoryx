# Theme System Design

## Context

Aegoryx already has an early design-system foundation in `resources/css/app.css`: the application uses `data-theme` on the `html` element, CSS variables named `--ui-*`, and a small `resources/js/app.js` helper backed by `localStorage`. The current visual implementation is still mostly dark-first. Many Blade and Livewire views use hard-coded Tailwind color utilities such as `bg-neutral-900`, `bg-neutral-950`, `text-neutral-100`, `border-neutral-800`, and semantic dark-only alert colors.

The requested change is a full dark/light theme system, not a partial inversion. Both themes must have intentional, separate color decisions. Theme choice must be visible globally and persisted per authenticated user in the database.

## Goals

- Provide a global theme switcher in both the admin panel and tenant panel.
- Persist the authenticated user's theme preference in the database.
- Switch themes immediately in the browser without a page reload.
- Keep `localStorage` as the fallback for logged-out/public screens and as a client-side cache.
- Use the browser color-scheme preference only for first-time visitors with no stored preference.
- Replace hard-coded dark Tailwind color classes in views with design-system classes, components, or CSS variables.
- Make light and dark mode equally deliberate and readable across admin, tenant, auth, public, and error screens.

## Non-Goals

- No unrelated business-logic changes.
- No redesign of navigation structure, permissions, CRM/CMS behavior, billing behavior, or tenancy behavior.
- No tenant-wide default theme setting in this phase. The preference is per user.
- No third theme such as `system` stored in the database. System preference is only a fallback before the user has chosen a theme.

## Data Model

Add a `theme` column to both authenticated user tables:

- system database: `identities.theme`
- tenant databases: `users.theme`

The allowed values are `light` and `dark`. The default should be `light` for existing rows and new rows. The models should cast the value through a shared enum or value object, for example `App\Support\Theme\ThemePreference`.

The enum should expose a simple value list for validation and a defensive helper for fallback resolution where useful.

## Theme Resolution

Theme resolution has two layers.

Server-side rendering:

- For authenticated admin pages, initial `data-theme` comes from `auth('admin')->user()->theme`.
- For authenticated tenant pages, initial `data-theme` comes from `auth('web')->user()->theme`.
- If the user has no theme for any reason, render `light`.
- Logged-out auth pages, public welcome, and error pages do not have a database-backed user. They should rely on the frontend bootstrap.

Client-side bootstrap:

- Read `localStorage.getItem('aegoryx.theme')`.
- If it is `light` or `dark`, apply it immediately.
- Otherwise use `window.matchMedia('(prefers-color-scheme: dark)')`.
- After authenticated page load, synchronize `localStorage` to the server-rendered user theme so future logged-out screens keep the last chosen value.

This keeps the first paint predictable for authenticated pages while preserving the user's last browser choice on public pages.

## Global Switcher

Add a reusable Blade component for the switcher, for example `resources/views/components/theme/switcher.blade.php`.

The component should:

- Render a compact segmented/toggle control suitable for app headers.
- Indicate the current mode.
- Include accessible labels for light and dark options.
- Avoid introducing visible instructional copy.
- Be usable in both `admin.layout` and `tenant.layout`.

The switcher should be placed in the global header action area for both panels. Auth/public screens may use the same component when layout space allows, but the required global placement is admin and tenant panel shells.

## Backend Update Flow

Add a small authenticated endpoint that updates the current user's theme without navigating away.

Route shape:

- admin domain: `PATCH /theme`, named `admin.theme.update`
- tenant panel: `PATCH /panel/theme`, named `tenant.theme.update`

Each endpoint should:

- Require authentication for the correct guard.
- Validate `theme` as `light` or `dark`.
- Update only the current authenticated user's `theme`.
- Return JSON with the saved theme.

The endpoint should not update tenant-level settings and should not allow changing another user's preference.

## Frontend Update Flow

Refactor `resources/js/app.js` into a small theme controller:

- Apply the initial theme before interactive behavior runs.
- Expose `window.aegoryxTheme.set(theme)` for immediate DOM changes.
- Store valid choices in `localStorage`.
- When a theme switcher is clicked, update `document.documentElement.dataset.theme` immediately.
- Send a `PATCH` request to the switcher's backend URL with the CSRF token and selected theme.
- If the backend request fails, keep the local visual change but surface a non-disruptive error state on the control.

The no-reload behavior is required. The DOM class/data attribute, `localStorage`, and backend persistence should all be updated from one user action.

## Design Tokens

Expand the design system around semantic tokens rather than direct Tailwind colors.

Required token groups:

- app background and primary text
- surface, raised surface, muted surface, and inset surface
- standard, subtle, and strong borders
- primary/accent colors and hover states
- nav item default, hover, active, and active text
- sidebar/header surfaces
- table header, row border, and row hover
- form controls, disabled states, placeholders, focus ring
- success, info, warning, and danger alerts
- badge variants
- code/preformatted surfaces
- prose preview text

Both `[data-theme='light']` and `[data-theme='dark']` should define intentional values. Light mode should not be generated by simply inverting dark mode.

## View Migration

Migrate the existing dark-first views to design-system classes and variables.

Scope includes:

- admin and tenant layouts
- admin auth screens
- tenant auth screens
- welcome page
- 403 page
- admin dashboard, sections, tenants, licenses, billing, audit, support, and security screens
- tenant dashboard, profile, settings, users, CMS, CRM, files, activity, modules, and security screens
- Livewire views under `resources/views/livewire/admin` and `resources/views/livewire/tenant`

The migration should prefer existing components such as `x-ui.card`, `x-ui.button`, `x-ui.badge`, `x-form.*`, `ui-table`, `ui-muted-panel`, `ui-label`, `ui-body`, and `ui-caption`. Add or extend UI classes only when a repeated pattern needs a stable semantic name.

Hard-coded semantic colors for alerts should move to reusable alert classes or a small alert component. Hard-coded link colors should move to `ui-link` or a variant.

## Error Handling

Invalid backend theme values should produce normal validation errors with a non-2xx response.

If the async save fails after a click, the UI should not reload. The switcher may mark itself with an error state and keep the local theme active. The next successful click should clear the error state. This favors responsiveness while still making persistence failure detectable.

## Testing

Add focused tests for:

- system `identities.theme` migration default and model cast
- tenant `users.theme` migration default and model cast
- admin theme update endpoint accepts `light` and `dark`
- tenant theme update endpoint accepts `light` and `dark`
- invalid theme values are rejected
- one user cannot update another user's theme through the endpoint
- existing tenant profile update still saves name and locale

Run the existing relevant feature tests for admin auth, tenant auth, tenant profile, and navigation if available.

## Verification

Implementation verification should include:

- `npm run build`
- relevant PHPUnit feature/unit tests
- `rg` audit for remaining hard-coded dark-first color classes in `resources/views`
- manual browser check of representative admin and tenant screens in both light and dark mode when the local app can run

Remaining hard-coded colors are acceptable only when they are intentionally semantic and theme-safe, such as `text-white` on a fixed accent button.

## Rollout Notes

Existing users receive `light` as their database default. Their browser may still have `aegoryx.theme` from earlier use. Once they visit an authenticated page, the database-backed theme should synchronize to `localStorage`.

The design-system README should be updated during implementation to document the database-backed preference, the switcher, and the rule that new views must use semantic UI tokens instead of raw palette utilities.
