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

- Opisać wymaganie `tenant_id` w tenant jobs.
- Dodać base trait/helper, jeśli potrzebny.
- Testować reset context po jobie.

## Subtaski

Brak.

## Acceptance Criteria

- Tenant job nie działa bez tenant id.
- Retry nie tworzy duplikatów tam, gdzie da się tego uniknąć.
- Context jest resetowany po jobie.

## Test Plan

- Queue fake tests.
- Integration test tenant job context.
