# Task: Tenant Navigation

## Cel

Zbudować tenant-aware navigation dla modułów CMS, CRM, Files i Settings.

## Zakres

- Navigation registry.
- Links per module.
- Active route state.

## Poza Zakresem

- Dynamic marketplace modules.

## Zależności

- Panel shell.
- Entitlements.

## Kroki

- Zdefiniować entries per module.
- Filtrować według access i permissions.
- Zachować backend enforcement.

## Subtaski

Brak.

## Acceptance Criteria

- Nawigacja nie pokazuje niedostępnych modułów.
- Bezpośredni URL nadal wymaga backend authorization.
- Tenant context jest wymagany.

## Test Plan

- Feature tests forbidden routes.
- UI smoke test navigation.
