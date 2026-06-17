# Backup And Restore

Backup musi obejmować całą bazę PostgreSQL oraz storage aplikacji. Model schema-per-tenant nie zakłada cross-schema FK, więc restore pojedynczego tenanta jest możliwy operacyjnie, ale wymaga ostrożnego odtworzenia jego schema i spójnych metadanych landlord.

## Minimalny backup

- full PostgreSQL dump
- osobna kopia `storage/app`
- zapis wersji aplikacji i `composer.lock`
- potwierdzenie liczby tenantów i listy schema

## Restore rehearsal

Co najmniej przed większym releasem operator powinien wykonać próbny restore na środowisku izolowanym:

1. odtworzyć bazę
2. odtworzyć storage
3. uruchomić `php artisan landlord:migrate --force`
4. uruchomić `php artisan tenants:migrate --force`
5. zweryfikować logowanie landlord i przykładowego tenanta
6. zweryfikować prywatny download pliku
7. uruchomić `php artisan aegoryx:smoke`
8. zapisać datę próby, wersję aplikacji, źródło backupu, wynik i właściciela follow-upów

## Okresowy harmonogram

- SaaS: minimum raz w miesiącu i przed większym releasem.
- Self-hosted: minimum raz na kwartał albo po zmianie infrastruktury backupu.
- Każdy failed rehearsal tworzy blocker release, dopóki nie ma właściciela i daty ponownej próby.

## Tenant-specific restore

Restore pojedynczego tenanta wymaga zgodności `tenants.schema_name` w landlord z odtwarzaną schema. Nie należy kopiować danych między schema ręcznie bez planu mapowania ID i audytu.
