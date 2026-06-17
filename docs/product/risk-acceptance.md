# Launch Risk Acceptance

Ryzyko launchowe może zostać zaakceptowane tylko wtedy, gdy ma właściciela, datę ponownego przeglądu i jasno opisany wpływ.

## Wymagany Format

| Field | Meaning |
| --- | --- |
| Risk | Konkretne ryzyko albo brakująca kontrolka. |
| Impact | Co realnie stanie się użytkownikowi/operatorowi, jeśli ryzyko się zmaterializuje. |
| Owner | Osoba odpowiedzialna za monitorowanie i domknięcie. |
| Accepted Until | Data ponownego przeglądu. |
| Mitigation | Tymczasowa kontrolka, monitoring albo manualny proces. |
| Exit Criteria | Warunek usunięcia ryzyka z listy. |

## Zasady

- Brak właściciela oznacza brak akceptacji.
- Brak daty review oznacza brak akceptacji.
- Ryzyka security, privacy, tenant isolation i backup/restore wymagają jawnej decyzji release ownera.
- Zaakceptowane ryzyka nie mogą być ukryte w backlogu technicznym; muszą być widoczne w launch checklist.
