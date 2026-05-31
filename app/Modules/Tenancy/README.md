# Tenancy Module

Owns tenant resolution, tenant context, schema switching, tenant migration commands, and safe PostgreSQL schema operations.

Tenant context must be initialized centrally and reset in `finally` blocks for long-running processes.
