# Task: Tenant Settings

## Status

Done.

## Cel

Dodać realny ekran ustawień tenanta zamiast placeholdera modułu.

## Zakres

- Podstawowe dane tenanta jako read-only.
- Edycja domyślnego języka tenanta.
- Autoryzacja edycji dla owner/admin.
- Tłumaczenia PL/EN/DE/ES/RU/FR.

## Poza Zakresem

- Samodzielna edycja domen.
- Billing self-service.
- Zmiana statusu, deployment type albo license type przez tenant panel.

## Zależności

- Tenant Panel.
- Locale per tenant/user.
- Tenant user roles.

## Kroki

- Dodać routing `tenant.settings.index` i `tenant.settings.update`.
- Dodać kontroler ustawień tenanta.
- Dodać widok Blade zgodny z design systemem.
- Dodać testy dostępu owner/viewer.

## Subtaski

Brak.

## Acceptance Criteria

- Owner/admin może zmienić domyślny język tenanta.
- Viewer widzi ustawienia jako read-only.
- Nowe teksty mają tłumaczenia we wszystkich obsługiwanych językach.

## Test Plan

- Feature test renderowania ustawień.
- Feature test zmiany locale przez ownera.
- Feature test blokady zapisu dla viewera.
