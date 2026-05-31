# Task: Rate Limits

## Cel

Dodać rate limiting dla public API.

## Zakres

- Public API limiter.
- Tenant/domain aware key.
- Sensowne response headers.

## Poza Zakresem

- Enterprise custom rate limits.

## Zależności

- Public tenant resolving.

## Kroki

- Zdefiniować limiter.
- Uwzględnić tenant/domain/IP.
- Testować limit exceeded.

## Subtaski

Brak.

## Acceptance Criteria

- Public API ma rate limit.
- Limit nie miesza tenantów.
- Przekroczenie limitu nie ujawnia danych.

## Test Plan

- Feature test throttling.
