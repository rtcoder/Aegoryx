# Epic 01: Platform Foundation

## Cel

Ustabilizować techniczny fundament Aegoryx: modular monolith, lokalny setup, standardy kodu, CI oraz test baseline.

## Dlaczego Jest Ważny

Bez tego każdy kolejny moduł będzie dodawał własne wzorce, co utrudni utrzymanie produktu przez mały zespół.

## Zależności

- Aktualny szkielet Laravel 13.
- `AGENT_CODING_GUIDELINES.md`.
- `migrations.md`.

## Taski

- [Modular Bootstrap](01-modular-bootstrap/)
- [Module Docs](02-module-docs/)
- [Local Dev Setup](03-local-dev-setup/)
- [Coding Standards](04-coding-standards/)
- [Test Baseline](05-test-baseline/)

## Definicja Ukończenia

- Laravel ładuje moduły jawnie i przewidywalnie.
- Repo ma opisany setup lokalny oraz standardy kodu.
- Testy i formatowanie są częścią codziennego workflow.
- Kolejne epici mogą dodawać kod bez wymyślania nowych struktur.
