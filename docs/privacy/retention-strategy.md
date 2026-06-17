# Strategia retencji danych

Ten dokument opisuje domyślne zasady retencji dla danych tenantów i warstwy landlord. Konkretne wartości są konfigurowane w `config/aegoryx.php` w sekcji `retention`.

## Kategorie danych

- `activity_entries` - historia aktywności tenantów, bez sekretów i z redakcją pól wrażliwych.
- `audit_logs` - logi audytowe landlord, zachowywane dłużej niż historia tenantów.
- `files` - metadane plików tenantów, z soft delete przed trwałym usunięciem.
- `exports` - prywatne pliki eksportów, zawsze z datą wygaśnięcia.

## Domyślne wartości

- historia aktywności tenantów: 365 dni
- logi audytowe landlord: 730 dni
- usunięte pliki prywatne: 30 dni
- pliki eksportów: 24 godziny

## Zasady wykonawcze

1. Twarde usunięcie nie powinno być wykonywane bez wcześniejszego soft delete lub jawnej ścieżki anonimizacji.
2. Eksporty danych prywatnych muszą być autoryzowane, audytowane i zapisywane jako pliki prywatne z `expires_at`.
3. Logi audytowe nie mogą przechowywać sekretów, tokenów, haseł ani pełnych danych płatniczych.
4. Dane potrzebne do rozliczeń, bezpieczeństwa i zgodności mogą mieć dłuższą retencję niż dane operacyjne tenantów.
5. Proces automatycznego purge powinien działać per tenant i raportować liczbę usuniętych rekordów.

## Kierunek implementacji purge

- czyścić wygasłe eksporty po `files.expires_at`
- usuwać trwale soft-deleted pliki po `deleted_files_days`
- anonimizować lub usuwać stare `activity_entries` po `activity_entries_days`
- zachować landlord `audit_logs` do `audit_logs_days`

## Komenda wykonawcza

Retencję uruchamia:

```bash
php artisan aegoryx:retention:purge
```

Przed automatyzacją harmonogramu można sprawdzić zakres czyszczenia:

```bash
php artisan aegoryx:retention:purge --dry-run
```

Komenda iteruje po aktywnych tenantach, używa aktualnego tenant contextu i raportuje liczbę usuniętych rekordów dla audit logs, activity entries, wygasłych plików oraz soft-deleted plików po okresie retencji.
