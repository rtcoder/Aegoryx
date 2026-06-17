<?php

namespace App\Modules\Crm\Actions;

use App\Models\Tenant\CrmTask;
use App\Models\Tenant\User;
use App\Modules\Audit\Enums\ActivityEntryAction;
use App\Modules\Audit\Services\ActivityLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

final readonly class DeleteTaskAction
{
    public function __construct(
        private ActivityLogger $activity,
    ) {}

    public function handle(CrmTask $task, User $actor): void
    {
        Gate::forUser($actor)->authorize('delete', $task);

        DB::transaction(function () use ($task, $actor): void {
            $before = [
                'subject_type' => $task->subject_type->value,
                'subject_id' => $task->subject_id,
                'title' => $task->title,
                'status' => $task->status->value,
                'due_date' => $task->due_date?->toDateString(),
                'assigned_to' => $task->assigned_to,
            ];

            $task->forceFill([
                'deleted_by' => $actor->id,
            ])->save();

            $task->delete();

            $this->activity->record(
                actorType: User::class,
                actorId: $actor->id,
                subject: $task,
                action: ActivityEntryAction::CrmTaskDeleted,
                description: __('activity.crm_task_deleted', ['task' => $task->title]),
                before: $before,
            );
        });
    }
}
