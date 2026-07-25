# Subtask: Create System Migration Path

## Zadanie

Utrzymać katalog `database/migrations/system` jako jedyne miejsce dla migracji public schema.

## Oczekiwane Zmiany

- System migrations trafiają wyłącznie do katalogu system.
- Komenda `system:migrate` używa tego path.
- Dokumentacja nie sugeruje plain `php artisan migrate` jako pełnego deployu.

## Obszary

- `database/migrations/system`
- `app/Console/Commands/MigrateSystemCommand.php`
- `migrations.md`

## Checklist

- [x] Katalog istnieje.
- [x] Komenda wskazuje poprawny path.
- [x] Test potwierdza tabelę w `public`.
