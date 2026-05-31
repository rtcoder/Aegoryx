# Subtask: Add Redaction Tests

## Zadanie

Dodać testy potwierdzające, że dane sensitive nie trafiają do activity/audit/logów plaintextem.

## Oczekiwane Zmiany

- Test update sensitive fields.
- Test activity payload.
- Test API Resource masking.

## Obszary

- `tests/Feature/Crm`
- `tests/Unit`

## Checklist

- [ ] Plain email nie występuje w activity.
- [ ] Plain phone nie występuje w activity.
- [ ] Resource pokazuje wartość masked.
