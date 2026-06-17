<?php

namespace App\Modules\Crm\Actions;

use App\Models\Tenant\CrmNote;
use App\Models\Tenant\User;
use App\Modules\Audit\Enums\ActivityEntryAction;
use App\Modules\Audit\Services\ActivityLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

final readonly class CreateNoteAction
{
    public function __construct(
        private ActivityLogger $activity,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data, User $actor): CrmNote
    {
        Gate::forUser($actor)->authorize('create', CrmNote::class);
        unset($data['subject']);

        return DB::transaction(function () use ($data, $actor): CrmNote {
            $note = CrmNote::query()->create([
                ...$data,
                'is_sensitive' => (bool) ($data['is_sensitive'] ?? false),
                'created_by' => $actor->id,
                'updated_by' => $actor->id,
            ]);

            $this->activity->record(
                actorType: User::class,
                actorId: $actor->id,
                subject: $note,
                action: ActivityEntryAction::CrmNoteCreated,
                description: __('activity.crm_note_created', ['subject' => $note->subjectLabel()]),
                after: $this->activityPayload($note),
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
