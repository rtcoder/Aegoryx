# License State Matrix

| State | Access | Data | Subscription | Operator Action |
| --- | --- | --- | --- | --- |
| trial | allowed | kept | not required | convert or expire |
| active | allowed | kept | required | monitor renewal |
| grace | allowed | kept | required | recover payment or license |
| expired | blocked | kept | required | renew or disable features |
| suspended | blocked | kept | required | manual review required |
| perpetual | allowed | kept | not required | verify installation periodically |

## Zasady

- Expired i suspended nie kasują danych tenantów.
- Perpetual działa bez SaaS subscription.
- Business modules nie czytają payloadów licencji bezpośrednio.
- Entitlements pozostają jedyną warstwą decyzyjną dla feature access.
