<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;

final class PlanFeature extends Model
{
    protected $fillable = [
        'plan_id',
        'feature_id',
        'enabled',
        'config',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'config' => 'array',
        ];
    }
}
