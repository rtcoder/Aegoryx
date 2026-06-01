# Task: Publish Flow

## Status

Done.

## Cel

Zbudować kontrolowany proces publikacji strony CMS z drafta do published snapshot.

## Zakres

- `PublishPageAction`.
- Authorization i entitlement check.
- Activity entry.
- Published snapshot update.

## Poza Zakresem

- Public API routing.

## Zależności

- Page model.
- Page revisions.
- Published snapshots.

## Kroki

- Walidować, czy draft może być opublikowany.
- W transakcji utworzyć snapshot i activity entry.
- Dispatchować efekty uboczne po commicie.

## Subtaski

- [Create Publish Action](subtasks/01-create-publish-action.md)
- [Record Publish Activity](subtasks/02-record-publish-activity.md)

## Acceptance Criteria

- Tylko uprawniony user publikuje.
- Published snapshot jest atomowo aktualizowany.
- Activity pokazuje kto i kiedy opublikował.

## Test Plan

- Feature test publish success.
- Feature test unauthorized publish.
- Activity test.
