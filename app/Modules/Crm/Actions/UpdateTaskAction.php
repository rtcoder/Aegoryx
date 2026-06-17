<?php

namespace App\Modules\Crm\Actions;

use App\Models\Tenant\CrmTask;
use App\Models\Tenant\User;
use App\Modules\Audit\Enums\ActivityEntryAction;
use App\Modules\Audit\Services\ActivityLogger;
use App\Modules\Crm\Enums\CrmTaskStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

final readonly class UpdateTaskAction
{
    public function __construct(
        private ActivityLogger $activity,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(CrmTask $task, array $data, User $actor): CrmTask
    {
        Gate::forUser($actor)->authorize('update', $task);
        unset($data['subject']);

        return DB::transaction(function () use ($task, $data, $actor): CrmTask {
            $before = $this->activityPayload($task);
            $status = CrmTaskStatus::from((string) $data['status']);

            $task->forceFill([
                ...$data,
                'completed_at' => $status === CrmTaskStatus::Completed ? ($task->completed_at ?? now()) : null,
                'updated_by' => $actor->id,
            ])->save();

            $this->activity->record(
                actorType: User::class,
                actorId: $actor->id,
                subject: $task,
                action: ActivityEntryAction::CrmTaskUpdated,
                description: __('activity.crm_task_updated', ['task' => $task->title]),
                before: $before,
                after: $this->activityPayload($task->refresh()),
            );

            return $task->refresh();
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function activityPayload(CrmTask $task): array
    {
        return [
            'subject_type' => $task->subject_type->value,
            'subject_id' => $task->subject_id,
            'title' => $task->title,
            'status' => $task->status->value,
            'due_date' => $task->due_date?->toDateString(),
            'assigned_to' => $task->assigned_to,
        ];
    }
}
