<?php

namespace App\Modules\Crm\Actions;

use App\Models\Tenant\CrmNote;
use App\Models\Tenant\User;
use App\Modules\Audit\Enums\ActivityEntryAction;
use App\Modules\Audit\Services\ActivityLogger;
use App\Modules\Crm\Support\CrmSubjectGuard;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

final readonly class UpdateNoteAction
{
    public function __construct(
        private ActivityLogger $activity,
        private CrmSubjectGuard $subjects,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(CrmNote $note, array $data, User $actor): CrmNote
    {
        Gate::forUser($actor)->authorize('update', $note);
        unset($data['subject']);
        $this->subjects->assertExists((string) ($data['subject_type'] ?? ''), (int) ($data['subject_id'] ?? 0));

        return DB::transaction(function () use ($note, $data, $actor): CrmNote {
            $before = $this->activityPayload($note);

            $note->forceFill([
                ...$data,
                'is_sensitive' => (bool) ($data['is_sensitive'] ?? false),
                'updated_by' => $actor->id,
            ])->save();

            $this->activity->record(
                actorType: User::class,
                actorId: $actor->id,
                subject: $note,
                action: ActivityEntryAction::CrmNoteUpdated,
                description: __('activity.crm_note_updated', ['subject' => $note->subjectLabel()]),
                before: $before,
                after: $this->activityPayload($note->refresh()),
            );

            return $note->refresh();
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function activityPayload(CrmNote $note): array
    {
        return [
            'subject_type' => $note->subject_type->value,
            'subject_id' => $note->subject_id,
            'body' => $note->is_sensitive ? '[redacted]' : $note->body,
            'is_sensitive' => $note->is_sensitive,
        ];
    }
}
