# Task: Subscription Mapping

## Cel

Mapować stan subskrypcji SaaS na wewnętrzne entitlementy.

## Zakres

- Subscription model.
- Provider status mapping.
- Webhook idempotency requirement.

## Poza Zakresem

- Pełna integracja Paddle checkout.
- Faktury UI.

## Zależności

- Plan limits.
- Billing provider decision.

## Kroki

- Zdefiniować wewnętrzne statusy.
- Mapować status providera na internal state.
- Zapisywać audit dla zmian billing.

## Subtaski

Brak.

## Acceptance Criteria

- Entitlements bazują na internal state.
- Webhook retry nie duplikuje zmian.
- Zmiany są audytowane.

## Test Plan

- Unit tests mappingu statusów.
- Test idempotencji webhook event id.
