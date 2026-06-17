<?php

namespace App\Modules\Crm\Actions;

use App\Models\Tenant\CrmNote;
use App\Models\Tenant\User;
use App\Modules\Audit\Enums\ActivityEntryAction;
use App\Modules\Audit\Services\ActivityLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

final readonly class DeleteNoteAction
{
    public function __construct(
        private ActivityLogger $activity,
    ) {}

    public function handle(CrmNote $note, User $actor): void
    {
        Gate::forUser($actor)->authorize('delete', $note);

        DB::transaction(function () use ($note, $actor): void {
            $before = [
                'subject_type' => $note->subject_type->value,
                'subject_id' => $note->subject_id,
                'body' => $note->is_sensitive ? '[redacted]' : $note->body,
                'is_sensitive' => $note->is_sensitive,
            ];

            $note->forceFill([
                'deleted_by' => $actor->id,
            ])->save();

            $note->delete();

            $this->activity->record(
                actorType: User::class,
                actorId: $actor->id,
                subject: $note,
                action: ActivityEntryAction::CrmNoteDeleted,
                description: __('activity.crm_note_deleted', ['subject' => $note->subjectLabel()]),
                before: $before,
            );
        });
    }
}
