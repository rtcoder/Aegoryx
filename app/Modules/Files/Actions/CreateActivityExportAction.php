<?php

namespace App\Modules\Files\Actions;

use App\Models\Tenant\ActivityEntry;
use App\Models\Tenant\TenantFile;
use App\Models\Tenant\User;
use App\Modules\Audit\Enums\ActivityEntryAction;
use App\Modules\Audit\Services\ActivityLogger;
use App\Modules\Audit\Support\RetentionPolicy;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final readonly class CreateActivityExportAction
{
    public function __construct(
        private ActivityLogger $activity,
        private RegisterFileMetadataAction $registerFileMetadata,
        private RetentionPolicy $retention,
    ) {}

    public function handle(User $actor): TenantFile
    {
        Gate::forUser($actor)->authorize('export', ActivityEntry::class);

        return DB::transaction(function () use ($actor): TenantFile {
            $disk = 'local';
            $path = sprintf('exports/activity/activity-export-%s-%s.json', now()->format('YmdHis'), Str::random(10));
            $expiresAt = $this->retention->exportFileExpiresAt();

            Storage::disk($disk)->put($path, $this->payload());

            $file = $this->registerFileMetadata->handle(
                disk: $disk,
                path: $path,
                originalName: 'activity-export.json',
                mimeType: 'application/json',
                ownerId: $actor->id,
                actor: $actor,
                expiresAt: $expiresAt,
            );

            $this->activity->record(
                actorType: User::class,
                actorId: $actor->id,
                subject: $file,
                action: ActivityEntryAction::ActivityExportCreated,
                description: __('activity.activity_export_created', ['file' => $file->original_name]),
                metadata: [
                    'file_id' => $file->id,
                    'expires_at' => $file->expires_at?->toISOString(),
                ],
            );

            return $file->refresh();
        });
    }

    private function payload(): string
    {
        $entries = ActivityEntry::query()
            ->oldest()
            ->get()
            ->map(fn (ActivityEntry $entry): array => [
                'id' => $entry->id,
                'actor_type' => $entry->actor_type,
                'actor_id' => $entry->actor_id,
                'subject_type' => $entry->subject_type,
                'subject_id' => $entry->subject_id,
                'action' => $entry->action->value,
                'description' => $entry->description,
                'before' => $entry->before_json,
                'after' => $entry->after_json,
                'metadata' => $entry->metadata_json,
                'ip' => $entry->ip,
                'user_agent' => $entry->user_agent,
                'created_at' => $entry->created_at?->toISOString(),
            ]);

        return json_encode([
            'generated_at' => now()->toISOString(),
            'entries' => $entries,
        ], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
    }
}
