<?php

namespace App\Modules\Audit\Services;

use App\Models\Tenant\ActivityEntry;
use App\Modules\Audit\Enums\ActivityEntryAction;
use Illuminate\Database\Eloquent\Model;

final readonly class ActivityLogger
{
    public function __construct(
        private RedactsActivityPayload $redactor,
    ) {}

    /**
     * @param  array<string, mixed>|null  $before
     * @param  array<string, mixed>|null  $after
     * @param  array<string, mixed>|null  $metadata
     */
    public function record(
        string $actorType,
        ?int $actorId,
        Model|string $subject,
        ActivityEntryAction $action,
        ?string $description = null,
        ?array $before = null,
        ?array $after = null,
        ?array $metadata = null,
        ?string $ip = null,
        ?string $userAgent = null,
    ): ActivityEntry {
        return ActivityEntry::query()->create([
            'actor_type' => $actorType,
            'actor_id' => $actorId,
            'subject_type' => is_string($subject) ? $subject : $subject::class,
            'subject_id' => is_string($subject) ? null : $subject->getKey(),
            'action' => $action,
            'description' => $description,
            'before_json' => $this->redactor->redact($before),
            'after_json' => $this->redactor->redact($after),
            'metadata_json' => $this->redactor->redact($metadata),
            'ip' => $ip,
            'user_agent' => $userAgent,
        ]);
    }
}
