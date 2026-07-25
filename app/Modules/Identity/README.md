# Identity Module

Owns global identities in the system schema, identity linking, and cross-tenant identity concerns.

Tenant business tables should not create cross-schema foreign keys to global identities.

## Tenant Roles And Permissions

Tenant roles are intentionally backed by an explicit permission matrix:

- `TenantPermission` defines stable permission keys used by policies and user helpers.
- `TenantRolePermissions` maps `TenantUserRole` values to those permissions.
- `User::hasTenantPermission()` is the preferred low-level check when adding new policy rules.

Keep role labels and UI copy in language files, but keep permission keys in code. Tenant admins should not be able to invent new system permissions through database rows.

## Password Reset

Tenant users and system superadmins use the same `PasswordResetTokens` support service. Tokens are stored hashed in `password_reset_tokens` and expire according to `auth.passwords.users.expire`.

System and tenant schemas both own a `password_reset_tokens` table. SQLite tests may run both schemas on one connection, so migrations guard table creation with `Schema::hasTable`.

Reset links are delivered through Laravel Notifications with translated mail copy. The request Livewire components keep the raw token only for local/test feedback, so production users receive the link through the configured mail channel.
