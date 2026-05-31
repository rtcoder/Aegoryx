# Task: Identity Model

## Cel

Zbudować globalny model identity w landlord schema, oddzielony od tenant users.

## Zakres

- Model i migracja `identities`.
- Logical reference z tenant users bez cross-schema FK.
- Podstawowe pola audytowe i soft deletes.

## Poza Zakresem

- Pełny login flow.
- OAuth providers.

## Zależności

- Landlord migrations.

## Kroki

- Zaprojektować `public.identities`.
- Ustalić relację logiczną z tenant user.
- Dodać test braku cross-schema FK.

## Subtaski

Brak.

## Acceptance Criteria

- Identity nie przechowuje danych tenantowych.
- Tenant schema nie ma FK do `public.identities`.
- Model jest gotowy pod SaaS i self-hosted.

## Test Plan

- Test migracji landlord.
- Schema inspection dla FK.
