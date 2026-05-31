<?php

namespace App\Models\Landlord;

use App\Modules\Billing\Enums\PlanStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Plan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'key',
        'name',
        'status',
        'billing_interval',
        'sort_order',
        'limits',
        'metadata',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'limits' => 'array',
            'metadata' => 'array',
            'status' => PlanStatus::class,
        ];
    }
}
