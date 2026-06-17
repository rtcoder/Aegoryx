# Task: Queue Setup

## Cel

Przygotować kolejki dla jobów tenantowych i systemowych.

## Zakres

- Queue connection.
- Tenant-aware job pattern.
- Retry/idempotency guidance.

## Poza Zakresem

- Horizon dashboard.

## Zależności

- Tenancy manager.
- Jobs table.

## Kroki

- Opisać wymaganie `tenant_id` w tenant jobs w `docs/operations/queues.md`.
- Dodać `App\Support\Queue\InteractsWithTenantContext`.
- Testować reset context po jobie.

## Subtaski

Brak.

## Acceptance Criteria

- [x] Tenant job nie działa bez tenant id.
- [x] Retry nie tworzy duplikatów tam, gdzie da się tego uniknąć.
- [x] Context jest resetowany po jobie.

## Test Plan

- [x] Unit test tenant job context.
