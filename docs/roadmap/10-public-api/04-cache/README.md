# Task: Cache

## Cel

Dodać tenant-aware cache dla publicznych opublikowanych treści.

## Zakres

- Cache keys z tenant id/schema.
- Page response cache.
- Cache key oparty o tenant, slug i timestamp published snapshot.

## Poza Zakresem

- CDN purge.

## Zależności

- Published page endpoints.
- Publish flow.

## Kroki

- Ustalić format cache key.
- Cacheować tylko published data.
- Używać timestampu published snapshot w cache key, żeby publish/update generował nowy cache key.

## Subtaski

Brak.

## Acceptance Criteria

- Cache key zawiera tenant context.
- Drafty nie trafiają do cache public API.
- Publish/update generuje nowy tenant-aware cache key.

## Test Plan

- Cache hit/miss tests.
- Invalidation tests.
