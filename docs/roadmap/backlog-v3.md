# Backlog V3

Ten plik zbiera większe prace po domknięciu backlogu V2 i launch readiness. Zakres dotyczy utwardzania produktu oraz lepszej obsługi operacyjnej.

## Identity I Permissions

- Done: wprowadzić jawny enum `TenantPermission`.
- Done: przenieść macierz rola -> uprawnienia do `TenantRolePermissions`.
- Done: przepiąć tenant user helpers i CMS publish policy na permission matrix.
- Done: dodać reset hasła dla tenant users i system superadminów.
- Done: wysyłać link resetu hasła przez Laravel Notifications, z dev linkiem tylko lokalnie/testowo.
- Done: dodać systemową tabelę `password_reset_tokens` oraz testową zgodność z SQLite.

## Public API

- Done: dodać endpoint listujący opublikowane snapshoty stron CMS.
- Done: zachować wersjonowanie `v1` i legacy aliasy read-only.
- Done: potwierdzić testem, że index nie zwraca draftów ani prywatnych pól.

## Operations

- Done: dodać `aegoryx:ops-report` z tabelą i JSON outputem.
- Done: dodać `ops-report` do preflightowej listy komend operacyjnych.
- Done: pokryć raport operacyjny testem systemowych metryk.
