# Backlog V2

Ten plik zbiera prace po domknięciu pierwszej roadmapy wykonawczej. Nie zastępuje tasków w epicach; służy do wyboru kolejnych iteracji bez udawania, że fundamenty nadal są pending.

## UX I Produktywność

- Done: rozszerzyć filtrowanie CRM o firmy, deale, notatki i zadania.
- Done: dodać sortowanie tabel tam, gdzie użytkownik porównuje wiele rekordów.
- Dodać puste stany z akcją główną dla CRM/CMS/Files.
- Dodać widoki detail dla plików i aktywności, jeśli lista zacznie być zbyt gęsta.

## Tenant Self-Service

- Done: dodać zgłaszanie domen tenanta w trybie request/pending verification.
- Done: dodać automatyczną weryfikację DNS TXT dla zgłoszonych domen.
- Done: pokazać billing/license summary jako read-only dla owner/admin.
- Done: dodać edycję preferencji użytkownika, w tym własnego locale.

## CMS I Public API

- Done: dodać preview opublikowanego contentu przed publikacją.
- Done: dodać cache invalidation po publish/unpublish.
- Rozważyć wersjonowany public API payload.

## Operations

- Done: rozszerzyć `aegoryx:preflight` o sprawdzenie storage i queue driverów.
- Dodać staging smoke test po deployu.
- Dodać okresowy restore rehearsal dla backupów.

## Commercial Readiness

- Doprecyzować macierz planów i limitów dla ofert SaaS.
- Dodać dashboard zdarzeń billing/licensing dla superadmina.
- Ustalić proces akceptacji ryzyk launchowych z datą review.
