<?php

namespace App\Modules\Files\Actions;

use App\Models\Tenant\TenantFile;
use App\Models\Tenant\User;
use App\Modules\Audit\Enums\ActivityEntryAction;
use App\Modules\Audit\Services\ActivityLogger;
use App\Modules\Entitlements\Services\EntitlementLimitEnforcer;
use App\Modules\Files\Enums\FileVisibility;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

final readonly class RegisterFileMetadataAction
{
    public function __construct(
        private ActivityLogger $activity,
        private EntitlementLimitEnforcer $limits,
    ) {}

    public function handle(
        string $disk,
        string $path,
        string $originalName,
        ?string $mimeType,
        ?int $ownerId,
        User $actor,
        ?CarbonInterface $expiresAt = null,
    ): TenantFile {
        Gate::forUser($actor)->authorize('create', TenantFile::class);

        return DB::transaction(function () use ($disk, $path, $originalName, $mimeType, $ownerId, $actor, $expiresAt): TenantFile {
            $storage = Storage::disk($disk);
            $contents = $storage->get($path);
            $sizeBytes = $storage->size($path);

            $this->limits->assertCanStoreFileBytes($sizeBytes);

            $file = TenantFile::query()->create([
                'disk' => $disk,
                'path' => $path,
                'original_name' => $originalName,
                'mime_type' => $mimeType,
                'size_bytes' => $sizeBytes,
                'checksum_sha256' => hash('sha256', $contents),
                'visibility' => FileVisibility::Private,
                'expires_at' => $expiresAt,
                'owner_id' => $ownerId,
                'created_by' => $actor->id,
                'updated_by' => $actor->id,
            ]);

            $this->activity->record(
                actorType: User::class,
                actorId: $actor->id,
                subject: $file,
                action: ActivityEntryAction::FileRegistered,
                description: __('activity.file_registered', ['file' => $file->original_name]),
                after: $this->activityPayload($file),
            );

            return $file->refresh();
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function activityPayload(TenantFile $file): array
    {
        return [
            'disk' => $file->disk,
            'path' => $file->path,
            'original_name' => $file->original_name,
            'mime_type' => $file->mime_type,
            'size_bytes' => $file->size_bytes,
            'checksum_sha256' => $file->checksum_sha256,
            'visibility' => $file->visibility->value,
            'expires_at' => $file->expires_at?->toISOString(),
            'owner_id' => $file->owner_id,
        ];
    }
}
