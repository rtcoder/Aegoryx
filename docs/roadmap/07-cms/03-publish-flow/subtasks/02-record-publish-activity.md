# Subtask: Record Publish Activity

## Zadanie

Zapisać activity entry dla publikacji i odpublikowania strony.

## Oczekiwane Zmiany

- Activity zawiera actor, subject, action i metadata.
- Nie zapisuje prywatnych danych panelowych.
- Historia strony pokazuje publish event.

## Obszary

- `app/Modules/Audit`
- `app/Modules/Cms/Actions`

## Checklist

- [ ] Action `published`.
- [ ] Action `unpublished`.
- [ ] Test actor i subject.
