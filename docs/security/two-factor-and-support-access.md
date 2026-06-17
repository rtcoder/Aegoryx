# Two Factor And Support Access

## 2FA Storage

Landlord `Identity` i tenant `User` mają pola:

- `two_factor_secret` - encrypted string
- `two_factor_recovery_codes` - encrypted array
- `two_factor_confirmed_at` - timestamp potwierdzenia

Recovery codes nie są przechowywane jako plaintext. Akcja `EnableTwoFactorAuthAction` zapisuje hashe kodów w zaszyfrowanej tablicy.

## Audit

Zmiany 2FA są zapisywane w `audit_logs` akcjami:

- `two_factor_enabled`
- `two_factor_disabled`

Audit log nie zawiera TOTP secret ani plaintext recovery codes.

## Support Access

Start support session wymaga:

- landlord superadmin
- aktywne 2FA
- jawny reason
- expiration

Support access bez 2FA jest blokowany w backendowej akcji `StartSupportSessionAction`, nie tylko w UI.

## Po MVP

Do zrobienia później:

- Livewire UI do konfiguracji 2FA
- challenge TOTP przy logowaniu
- zużywanie pojedynczych recovery codes
- ewentualne WebAuthn/hardware keys
