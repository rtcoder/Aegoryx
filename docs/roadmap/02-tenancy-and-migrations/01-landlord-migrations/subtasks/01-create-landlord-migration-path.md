# Subtask: Create Landlord Migration Path

## Zadanie

Utrzymać katalog `database/migrations/landlord` jako jedyne miejsce dla migracji public schema.

## Oczekiwane Zmiany

- Landlord migrations trafiają wyłącznie do katalogu landlord.
- Komenda `landlord:migrate` używa tego path.
- Dokumentacja nie sugeruje plain `php artisan migrate` jako pełnego deployu.

## Obszary

- `database/migrations/landlord`
- `app/Console/Commands/MigrateLandlordCommand.php`
- `migrations.md`

## Checklist

- [x] Katalog istnieje.
- [x] Komenda wskazuje poprawny path.
- [x] Test potwierdza tabelę w `public`.
