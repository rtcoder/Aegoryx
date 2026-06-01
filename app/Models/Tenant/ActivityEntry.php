<?php

namespace App\Models\Tenant;

use App\Modules\Audit\Enums\ActivityEntryAction;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $actor_type
 * @property int|null $actor_id
 * @property string $subject_type
 * @property int|null $subject_id
 * @property ActivityEntryAction $action
 * @property string|null $description
 * @property array<string, mixed>|null $before_json
 * @property array<string, mixed>|null $after_json
 * @property array<string, mixed>|null $metadata_json
 * @property string|null $ip
 * @property string|null $user_agent
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'actor_type',
    'actor_id',
    'subject_type',
    'subject_id',
    'action',
    'description',
    'before_json',
    'after_json',
    'metadata_json',
    'ip',
    'user_agent',
])]
final class ActivityEntry extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'after_json' => 'array',
            'action' => ActivityEntryAction::class,
            'before_json' => 'array',
            'metadata_json' => 'array',
        ];
    }
}
