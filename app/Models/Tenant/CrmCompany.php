<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string|null $website
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $notes
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
#[Fillable([
    'name',
    'website',
    'email',
    'phone',
    'notes',
    'created_by',
    'updated_by',
    'deleted_by',
])]
final class CrmCompany extends Model
{
    use SoftDeletes;

    /**
     * @return BelongsToMany<CrmContact, $this>
     */
    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(CrmContact::class, 'crm_company_contact', 'company_id', 'contact_id')
            ->withPivot('role')
            ->withTimestamps();
    }
}
