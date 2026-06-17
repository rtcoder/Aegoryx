# Backlog V2

Ten plik zbiera prace po domknięciu pierwszej roadmapy wykonawczej. Nie zastępuje tasków w epicach; służy do wyboru kolejnych iteracji bez udawania, że fundamenty nadal są pending.

## UX I Produktywność

- Rozszerzyć filtrowanie CRM o firmy, deale, notatki i zadania.
- Dodać sortowanie tabel tam, gdzie użytkownik porównuje wiele rekordów.
- Dodać puste stany z akcją główną dla CRM/CMS/Files.
- Dodać widoki detail dla plików i aktywności, jeśli lista zacznie być zbyt gęsta.

## Tenant Self-Service

- Dodać zarządzanie domenami tenanta w trybie request/verification.
- Pokazać billing/license summary jako read-only dla owner/admin.
- Dodać edycję preferencji użytkownika, w tym własnego locale.

## CMS I Public API

- Dodać preview opublikowanego contentu przed publikacją.
- Dodać cache invalidation po publish/unpublish.
- Rozważyć wersjonowany public API payload.

## Operations

- Rozszerzyć `aegoryx:preflight` o sprawdzenie storage i queue driverów.
- Dodać staging smoke test po deployu.
- Dodać okresowy restore rehearsal dla backupów.

## Commercial Readiness

- Doprecyzować macierz planów i limitów dla ofert SaaS.
- Dodać dashboard zdarzeń billing/licensing dla superadmina.
- Ustalić proces akceptacji ryzyk launchowych z datą review.
