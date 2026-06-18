# PublicApi Module

Owns public read-only CMS API endpoints.

Only published CMS content may be exposed. No CRM data, drafts, private user data, or write endpoints belong here.

## CMS Endpoints

- `GET /api/public/v1/cms/pages` lists published page snapshots for the resolved tenant.
- `GET /api/public/v1/cms/pages/{slug}` returns one published page snapshot.
- Legacy non-versioned aliases stay read-only for compatibility.

Responses must expose the public snapshot only: slug, title, content and published timestamp.
