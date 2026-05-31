# Epic 11: Operations Deployment

## Cel

Przygotować Aegoryx do stabilnego deployu, kolejek, Horizon, backupów i self-hosted operations.

## Dlaczego Jest Ważny

Produkt ma działać jako SaaS i self-hosted bez osobnego dużego zespołu DevOps.

## Zależności

- Tenancy migrations.
- Queue jobs.
- Billing/licensing basics.

## Taski

- [Env Strategy](01-env-strategy/)
- [Queue Setup](02-queue-setup/)
- [Horizon](03-horizon/)
- [Deploy Commands](04-deploy-commands/)
- [Backup Notes](05-backup-notes/)
- [Production Migration Checklist](06-production-migration-checklist/)

## Definicja Ukończenia

- Deploy ma opisane komendy i kolejność.
- Queue workers resetują tenant context.
- Self-hosted setup ma minimalną dokumentację operacyjną.
