# Epic 07: CMS

## Cel

Zbudować CMS z draftami, rewizjami, publikacją i snapshotami gotowymi dla publicznego read API.

## Dlaczego Jest Ważny

CMS musi rozdzielać pracę redakcyjną od opublikowanych treści, żeby publiczne API nigdy nie pokazało draftów ani danych panelowych.

## Zależności

- Tenant Panel.
- Audit/activity foundation.
- Public API foundation.

## Status

- Done: Page Model, Page Revisions, Publish Flow, Blocks Content Structure, Published Snapshots, CMS Activity History.

## Taski

- [x] [Page Model](01-page-model/)
- [x] [Page Revisions](02-page-revisions/)
- [x] [Publish Flow](03-publish-flow/)
- [x] [Blocks Content Structure](04-blocks-content-structure/)
- [x] [Published Snapshots](05-published-snapshots/)
- [x] [CMS Activity History](06-cms-activity-history/)

## Definicja Ukończenia

- CMS ma draft, revisions i published snapshot.
- Publikacja jest audytowana.
- Public API konsumuje tylko published data.
