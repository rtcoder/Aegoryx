# Subtask: Add Redaction Tests

## Zadanie

Dodać testy potwierdzające, że dane sensitive nie trafiają do activity/audit/logów plaintextem.

## Oczekiwane Zmiany

- Test update sensitive fields.
- Test activity payload.
- Test sensitive note masking.

## Obszary

- `tests/Feature/Crm`
- `tests/Unit`

## Checklist

- [x] Plain email nie występuje w activity.
- [x] Plain phone nie występuje w activity.
- [x] Sensitive note pokazuje wartość masked w activity.
