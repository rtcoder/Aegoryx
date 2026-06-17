# Task: Subscription Mapping

## Status

Done for internal subscription mapping and webhook idempotency foundation.

## Implemented Notes

- `active` i `trialing` subscription status z aktywnym planem zasilają entitlements.
- `SyncSubscriptionFromProviderEventAction` mapuje status providera na `SubscriptionStatus`.
- `/api/billing/webhooks/{provider}` przyjmuje podpisane eventy billingowe.
- `BillingEvent` zapewnia idempotency po `provider + provider_event_id`.
- Zmiany subskrypcji są audytowane jako `billing_subscription_synced`.
- Pełny Paddle checkout nadal jest poza obecnym zakresem implementacji.

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
