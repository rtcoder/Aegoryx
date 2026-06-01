# Subtask: Reset Search Path Safely

## Zadanie

Wymusić reset `search_path` po każdej tenant-scoped operacji.

## Oczekiwane Zmiany

- `TenancyManager::end()` wykonuje reset context.
- Commands, jobs i middleware używają `try/finally`.
- Long-running process nie dziedziczy poprzedniego tenanta.

## Obszary

- `app/Services/Tenancy`
- `app/Console/Commands`
- przyszłe tenant jobs

## Checklist

- [x] Test resetu contextu.
- [x] Brak ręcznego `SET search_path` w kontrolerach.
- [x] Dokumentacja job pattern gotowa.
