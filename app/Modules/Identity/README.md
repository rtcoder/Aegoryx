# Identity Module

Owns global identities in the landlord schema, identity linking, and cross-tenant identity concerns.

Tenant business tables should not create cross-schema foreign keys to global identities.

## Tenant Roles And Permissions

Tenant roles are intentionally backed by an explicit permission matrix:

- `TenantPermission` defines stable permission keys used by policies and user helpers.
- `TenantRolePermissions` maps `TenantUserRole` values to those permissions.
- `User::hasTenantPermission()` is the preferred low-level check when adding new policy rules.

Keep role labels and UI copy in language files, but keep permission keys in code. Tenant admins should not be able to invent new system permissions through database rows.
