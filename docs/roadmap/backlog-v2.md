# Backlog V2

Ten plik zbiera prace po domknięciu pierwszej roadmapy wykonawczej. Nie zastępuje tasków w epicach; służy do wyboru kolejnych iteracji bez udawania, że fundamenty nadal są pending.

## UX I Produktywność

- Done: rozszerzyć filtrowanie CRM o firmy, deale, notatki i zadania.
- Done: dodać sortowanie tabel tam, gdzie użytkownik porównuje wiele rekordów.
- Done: dodać puste stany z akcją główną dla CRM/CMS/Files.
- Dodać widoki detail dla plików i aktywności, jeśli lista zacznie być zbyt gęsta.

## Tenant Self-Service

- Done: dodać zgłaszanie domen tenanta w trybie request/pending verification.
- Done: dodać automatyczną weryfikację DNS TXT dla zgłoszonych domen.
- Done: pokazać billing/license summary jako read-only dla owner/admin.
- Done: dodać edycję preferencji użytkownika, w tym własnego locale.

## CMS I Public API

- Done: dodać preview opublikowanego contentu przed publikacją.
- Done: dodać cache invalidation po publish/unpublish.
- Done: rozważyć i dodać wersjonowany public API payload.

## Operations

- Done: rozszerzyć `aegoryx:preflight` o sprawdzenie storage i queue driverów.
- Done: dodać staging smoke test po deployu.
- Done: dodać okresowy restore rehearsal dla backupów.

## Commercial Readiness

- Done: doprecyzować macierz planów i limitów dla ofert SaaS.
- Done: dodać dashboard zdarzeń billing/licensing dla superadmina.
- Done: ustalić proces akceptacji ryzyk launchowych z datą review.
- Done: egzekwować limity planów w CMS, CRM i Files.
- Done: dodać seedery planów i feature defaults.
- Done: dodać komendę purge dla retencji.
