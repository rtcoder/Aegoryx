# Aegoryx Modules

Aegoryx is a modular monolith. Each bounded context lives in `app/Modules/{Module}` and owns its routes, providers, actions, requests, resources, models, policies, jobs, queries, services, and support code.

Preferred module structure:

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
Providers/
```

Module providers are registered in `config/aegoryx.php` and loaded by `App\Providers\AegoryxModuleServiceProvider`.

Keep these boundaries:

- Tenant business data belongs in tenant schemas.
- Landlord/system data belongs in the public schema.
- Controllers stay thin.
- Business changes go through Actions or Services.
- Important changes must record activity/audit history.
- Tenant-scoped jobs must carry `tenant_id`.
- Module access should go through Entitlements, not direct billing/license checks.
