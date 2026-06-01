# Task: Public Tenant Resolving

## Status

Done.

## Cel

Rozwiązywać tenanta dla publicznego API na podstawie domeny lub publicznego identyfikatora.

## Zakres

- Domain lookup.
- Tenant context initialization.
- Failure states 404/disabled.

## Poza Zakresem

- Panel tenant resolving.

## Zależności

- Tenant domains landlord table.
- Tenancy manager.

## Kroki

- Dodać resolver dla public requestów.
- Sprawdzać status tenanta/domeny.
- Resetować context po request.

## Subtaski

Brak.

## Acceptance Criteria

- Nieznana domena zwraca 404.
- Suspended tenant nie zwraca content.
- Search path jest resetowany.

## Test Plan

- Feature tests known/unknown/suspended domain.
