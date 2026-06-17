<?php

namespace App\Modules\Files\Actions;

use App\Models\Tenant\TenantFile;
use App\Models\Tenant\User;
use App\Modules\Audit\Enums\ActivityEntryAction;
use App\Modules\Audit\Services\ActivityLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

final readonly class DeleteFileMetadataAction
{
    public function __construct(
        private ActivityLogger $activity,
    ) {}

    public function handle(TenantFile $file, User $actor): void
    {
        Gate::forUser($actor)->authorize('delete', $file);

        DB::transaction(function () use ($file, $actor): void {
            $before = [
                'disk' => $file->disk,
                'path' => $file->path,
                'original_name' => $file->original_name,
                'owner_id' => $file->owner_id,
            ];

            $file->forceFill([
                'deleted_by' => $actor->id,
            ])->save();

            $file->delete();

            $this->activity->record(
                actorType: User::class,
                actorId: $actor->id,
                subject: $file,
                action: ActivityEntryAction::FileDeleted,
                description: __('activity.file_deleted', ['file' => $file->original_name]),
                before: $before,
            );
        });
    }
}
