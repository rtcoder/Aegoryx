<?php

namespace App\Models\Tenant;

use App\Modules\Crm\Enums\CrmSubjectType;
use App\Modules\Crm\Enums\CrmTaskStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property CrmSubjectType $subject_type
 * @property int $subject_id
 * @property string $title
 * @property string|null $description
 * @property CrmTaskStatus $status
 * @property Carbon|null $due_date
 * @property int|null $assigned_to
 * @property Carbon|null $completed_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
#[Fillable([
    'subject_type',
    'subject_id',
    'title',
    'description',
    'status',
    'due_date',
    'assigned_to',
    'completed_at',
    'created_by',
    'updated_by',
    'deleted_by',
])]
final class CrmTask extends Model
{
    use SoftDeletes;

    /**
     * @return BelongsTo<User, $this>
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * @return BelongsTo<CrmCompany, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(CrmCompany::class, 'subject_id');
    }

    /**
     * @return BelongsTo<CrmContact, $this>
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(CrmContact::class, 'subject_id');
    }

    /**
     * @return BelongsTo<CrmDeal, $this>
     */
    public function deal(): BelongsTo
    {
        return $this->belongsTo(CrmDeal::class, 'subject_id');
    }

    public function subjectLabel(): string
    {
        return match ($this->subject_type) {
            CrmSubjectType::Company => $this->company?->name ?? __('common.not_set'),
            CrmSubjectType::Contact => trim(($this->contact?->first_name ?? '').' '.($this->contact?->last_name ?? '')) ?: __('common.not_set'),
            CrmSubjectType::Deal => $this->deal?->title ?? __('common.not_set'),
        };
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'subject_type' => CrmSubjectType::class,
            'status' => CrmTaskStatus::class,
            'due_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }
}
