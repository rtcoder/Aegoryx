# Task: Fortify Jetstream Decision

## Status

Done.

## Decyzja

Aegoryx używa własnej warstwy auth UI opartej o Laravel session guards i Livewire.

Na tym etapie nie używamy Jetstream ani Inertia, bo panel ma być Livewire-first, a tenant context musi być jawnie inicjalizowany przed zapytaniami auth do tenant schema. Fortify może zostać użyty później jako backendowa warstwa 2FA/password reset, ale bez generowania UI i bez narzucania stacka frontendowego.

## Konsekwencje

- Landlord auth używa guard `landlord` i modelu `Identity`.
- Tenant auth używa guard `web` i modelu `App\Models\Tenant\User` po ustawieniu tenant context.
- UI logowania pozostaje w Livewire.
- 2FA będzie projektowane jako własny moduł Security albo jako selektywna integracja Fortify bez Jetstream UI.

## Cel

Podjąć i udokumentować decyzję, czy użyć Fortify, Jetstream, czy własnej warstwy auth UI.

## Zakres

- Porównanie Fortify i Jetstream dla Inertia.
- Wpływ na tenant context i 2FA.
- Decyzja zapisana w dokumentacji.

## Poza Zakresem

- Implementacja pełnego UI logowania.

## Zależności

- Tenant Panel direction.
- Security requirements.

## Kroki

- Ocenić integrację z Laravel 13.
- Sprawdzić wpływ na multi-tenancy.
- Zapisać decyzję i konsekwencje.

## Subtaski

Brak.

## Acceptance Criteria

- Decyzja jest jednoznaczna.
- Dokument wskazuje, czego nie używamy i dlaczego.
- Wybrana ścieżka obsługuje 2FA.

## Test Plan

- Review dokumentu decyzyjnego.
