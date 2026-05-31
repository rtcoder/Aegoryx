# Task: Cache

## Cel

Dodać tenant-aware cache dla publicznych opublikowanych treści.

## Zakres

- Cache keys z tenant id/schema.
- Page response cache.
- Invalidation po publish/unpublish.

## Poza Zakresem

- CDN purge.

## Zależności

- Published page endpoints.
- Publish flow.

## Kroki

- Ustalić format cache key.
- Cacheować tylko published data.
- Czyścić cache po publikacji.

## Subtaski

Brak.

## Acceptance Criteria

- Cache key zawiera tenant context.
- Drafty nie trafiają do cache public API.
- Publish/unpublish invaliduje właściwy tenant cache.

## Test Plan

- Cache hit/miss tests.
- Invalidation tests.
