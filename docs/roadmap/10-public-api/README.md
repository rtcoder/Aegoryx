# Epic 10: Public API

## Cel

Udostępnić publiczne read-only API dla opublikowanych treści CMS.

## Dlaczego Jest Ważny

Publiczne strony klientów mają być odsprzęgnięte od panelu i konsumować tylko bezpieczne, opublikowane dane.

## Zależności

- Tenancy resolving.
- CMS published snapshots.
- Cache strategy.

## Taski

- [Public Tenant Resolving](01-public-tenant-resolving/)
- [Published Page Endpoints](02-published-page-endpoints/)
- [Rate Limits](03-rate-limits/)
- [Cache](04-cache/)
- [CORS Allow List](05-cors-allow-list/)
- [No Private Data Tests](06-no-private-data-tests/)

## Definicja Ukończenia

- API jest read-only.
- Zwraca tylko published content.
- Ma rate limit, cache i testy braku danych prywatnych.
