# Task: Modular Bootstrap

## Cel

Utrzymać jawny bootstrap modułów Aegoryx i upewnić się, że każdy moduł może rejestrować provider, routes, policies, commands i bindings.

## Zakres

- Konfiguracja listy modułów.
- Provider ładujący moduły.
- Bazowy `ModuleServiceProvider`.
- Test potwierdzający rejestrację modułów.

## Poza Zakresem

- Implementacja logiki biznesowej modułów.
- Dynamiczne odkrywanie modułów z filesystemu.

## Zależności

- Laravel bootstrap.
- `AGENT_CODING_GUIDELINES.md`.

## Kroki

- Zweryfikować `config/aegoryx.php`.
- Upewnić się, że provider modułów jest w `bootstrap/providers.php`.
- Dodać test architektoniczny dla modułów.

## Subtaski

Brak. Task jest wystarczająco mały.

## Acceptance Criteria

- Wszystkie włączone moduły mają istniejące providery.
- Artisan startuje bez błędów.
- Test modułów przechodzi.

## Test Plan

- `php artisan about --only=environment`
- `php artisan route:list`
- `composer test`
