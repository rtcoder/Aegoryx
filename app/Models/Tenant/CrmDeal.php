<?php

namespace App\Models\Tenant;

use App\Modules\Crm\Enums\CrmDealStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $title
 * @property int|null $company_id
 * @property int|null $contact_id
 * @property CrmDealStatus $status
 * @property string|null $value_amount
 * @property string|null $currency
 * @property Carbon|null $expected_close_date
 * @property string|null $notes
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
#[Fillable([
    'title',
    'company_id',
    'contact_id',
    'status',
    'value_amount',
    'currency',
    'expected_close_date',
    'notes',
    'created_by',
    'updated_by',
    'deleted_by',
])]
final class CrmDeal extends Model
{
    use SoftDeletes;

    /**
     * @return BelongsTo<CrmCompany, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(CrmCompany::class);
    }

    /**
     * @return BelongsTo<CrmContact, $this>
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(CrmContact::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => CrmDealStatus::class,
            'expected_close_date' => 'date',
        ];
    }
}
