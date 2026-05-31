# Epic 07: CMS

## Cel

Zbudować CMS z draftami, rewizjami, publikacją i snapshotami gotowymi dla publicznego read API.

## Dlaczego Jest Ważny

CMS musi rozdzielać pracę redakcyjną od opublikowanych treści, żeby publiczne API nigdy nie pokazało draftów ani danych panelowych.

## Zależności

- Tenant Panel.
- Audit/activity foundation.
- Public API foundation.

## Taski

- [Page Model](01-page-model/)
- [Page Revisions](02-page-revisions/)
- [Publish Flow](03-publish-flow/)
- [Blocks Content Structure](04-blocks-content-structure/)
- [Published Snapshots](05-published-snapshots/)
- [CMS Activity History](06-cms-activity-history/)

## Definicja Ukończenia

- CMS ma draft, revisions i published snapshot.
- Publikacja jest audytowana.
- Public API konsumuje tylko published data.
