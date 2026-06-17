<?php

namespace App\Modules\Files\Actions;

use App\Models\Tenant\TenantFile;
use App\Models\Tenant\User;
use App\Modules\Audit\Enums\ActivityEntryAction;
use App\Modules\Audit\Services\ActivityLogger;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

final readonly class DownloadPrivateFileAction
{
    public function __construct(
        private ActivityLogger $activity,
    ) {}

    public function handle(TenantFile $file, User $actor): StreamedResponse
    {
        Gate::forUser($actor)->authorize('download', $file);

        $this->activity->record(
            actorType: User::class,
            actorId: $actor->id,
            subject: $file,
            action: ActivityEntryAction::FileDownloaded,
            description: __('activity.file_downloaded', ['file' => $file->original_name]),
            metadata: [
                'disk' => $file->disk,
                'file_id' => $file->id,
                'owner_id' => $file->owner_id,
            ],
        );

        return Storage::disk($file->disk)->download(
            $file->path,
            $file->original_name,
            array_filter([
                'Content-Type' => $file->mime_type,
            ]),
        );
    }
}
