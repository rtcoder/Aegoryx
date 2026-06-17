# Authorization Audit

## Zakres

Przegląd obejmuje akcje zapisu w CMS, CRM, Files, Admin Console oraz operacje eksportu.

## Backend Checks

- CRM create/update/delete używa policy przez akcje domenowe i kontrolery.
- Files download/delete/export używa `TenantFilePolicy` oraz `ActivityEntryPolicy`.
- Admin Console jest chroniony middleware `EnsureLandlordAuthenticated`.
- License verification wymaga landlord `Identity` z `is_super_admin=true`.
- Tenant panel routes wymagają tenant auth i feature entitlement middleware.

## Gates

- UI nie jest źródłem bezpieczeństwa.
- Każdy write endpoint musi mieć FormRequest authorize, Gate/Policy albo middleware auth z jednoznacznym warunkiem.
- Nowe moduły tenantowe muszą mieć test guest/unauthorized dla zapisu.

## Brak Blockerów

Na obecnym etapie brak znanych blockerów authorization. Role tenantowe są jeszcze uproszczone, więc bardziej granularne permissions pozostają backlogiem po MVP.
