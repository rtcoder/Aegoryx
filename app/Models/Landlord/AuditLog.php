<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;

final class AuditLog extends Model
{
    protected $fillable = [
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
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'after_json' => 'array',
            'before_json' => 'array',
            'metadata_json' => 'array',
        ];
    }
}
