# Epic 10: Public API

## Cel

Udostępnić publiczne read-only API dla opublikowanych treści CMS.

## Dlaczego Jest Ważny

Publiczne strony klientów mają być odsprzęgnięte od panelu i konsumować tylko bezpieczne, opublikowane dane.

## Zależności

- Tenancy resolving.
- CMS published snapshots.
- Cache strategy.

## Status

- Done: Public Tenant Resolving, Published Page Endpoints, Rate Limits, Cache, CORS Allow List, No Private Data Tests.

## Taski

- [x] [Public Tenant Resolving](01-public-tenant-resolving/)
- [x] [Published Page Endpoints](02-published-page-endpoints/)
- [x] [Rate Limits](03-rate-limits/)
- [x] [Cache](04-cache/)
- [x] [CORS Allow List](05-cors-allow-list/)
- [x] [No Private Data Tests](06-no-private-data-tests/)

## Definicja Ukończenia

- API jest read-only.
- Zwraca tylko published content.
- Ma rate limit, cache i testy braku danych prywatnych.
