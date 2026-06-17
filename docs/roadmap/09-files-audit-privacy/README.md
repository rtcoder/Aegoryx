# Epic 09: Files Audit Privacy

## Cel

Zbudować moduły plików, activity history, audit log oraz podstawy retention/GDPR.

## Dlaczego Jest Ważny

Prywatność i audyt są częścią produktu, nie dodatkiem. Każda istotna operacja musi odpowiadać: kto, kiedy, co i w jakim kontekście.

## Zależności

- Tenancy.
- Identity/auth/security.
- Tenant Panel.

## Status

- Done: File Metadata, Private File Access, Audit Log Model, Activity Entries, Export Audit, Retention Strategy.
- Pending: Brak.

## Taski

- [x] [File Metadata](01-file-metadata/)
- [x] [Private File Access](02-private-file-access/)
- [x] [Audit Log Model](03-audit-log-model/)
- [x] [Activity Entries](04-activity-entries/)
- [x] [Export Audit](05-export-audit/)
- [x] [Retention Strategy](06-retention-strategy/)

## Definicja Ukończenia

- Private downloads są autoryzowane i audytowane.
- Activity entries obsługują główne modele.
- Audit log nie zapisuje sekretów ani plaintext danych wrażliwych.
