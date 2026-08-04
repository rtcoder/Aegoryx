<?php

namespace App\Modules\Crm\Actions;

use App\Models\Tenant\CrmTask;
use App\Models\Tenant\User;
use App\Modules\Audit\Enums\ActivityEntryAction;
use App\Modules\Audit\Services\ActivityLogger;
use App\Modules\Crm\Enums\CrmTaskStatus;
use App\Modules\Crm\Support\CrmAssigneeGuard;
use App\Modules\Crm\Support\CrmSubjectGuard;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

final readonly class CreateTaskAction
{
    public function __construct(
        private ActivityLogger $activity,
        private CrmSubjectGuard $subjects,
        private CrmAssigneeGuard $assignees,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data, User $actor): CrmTask
    {
        Gate::forUser($actor)->authorize('create', CrmTask::class);
        unset($data['subject']);
        $this->subjects->assertExists((string) ($data['subject_type'] ?? ''), (int) ($data['subject_id'] ?? 0));
        $this->assignees->assertAssignable($data['assigned_to'] ?? null);

        return DB::transaction(function () use ($data, $actor): CrmTask {
            $status = CrmTaskStatus::from((string) $data['status']);
            $task = CrmTask::query()->create([
                ...$data,
                'completed_at' => $status === CrmTaskStatus::Completed ? now() : null,
                'created_by' => $actor->id,
                'updated_by' => $actor->id,
            ]);

            $this->activity->record(
                actorType: User::class,
                actorId: $actor->id,
                subject: $task,
                action: ActivityEntryAction::CrmTaskCreated,
                description: __('activity.crm_task_created', ['task' => $task->title]),
                after: $this->activityPayload($task),
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
