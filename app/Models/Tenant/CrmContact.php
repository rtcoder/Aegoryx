<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;

/**
 * @property int $id
 * @property string $first_name
 * @property string|null $last_name
 * @property string|null $email
 * @property string|null $email_encrypted
 * @property string|null $email_hash
 * @property string|null $phone
 * @property string|null $phone_encrypted
 * @property string|null $phone_hash
 * @property string|null $position
 * @property string|null $notes
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
#[Fillable([
    'first_name',
    'last_name',
    'email',
    'email_encrypted',
    'email_hash',
    'phone',
    'phone_encrypted',
    'phone_hash',
    'position',
    'notes',
    'created_by',
    'updated_by',
    'deleted_by',
])]
final class CrmContact extends Model
{
    use SoftDeletes;

    /**
     * @return BelongsToMany<CrmCompany, $this>
     */
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(CrmCompany::class, 'crm_company_contact', 'contact_id', 'company_id')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * @return Attribute<string|null, string|null>
     */
    protected function email(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->decryptNullable($this->attributes['email_encrypted'] ?? null),
            set: fn (?string $value): array => [
                'email_encrypted' => $this->encryptNullable($value),
                'email_hash' => self::hashLookup($value),
            ],
        );
    }

    /**
     * @return Attribute<string|null, string|null>
     */
    protected function phone(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->decryptNullable($this->attributes['phone_encrypted'] ?? null),
            set: fn (?string $value): array => [
                'phone_encrypted' => $this->encryptNullable($value),
                'phone_hash' => self::hashLookup($value),
            ],
        );
    }

    public static function hashLookup(?string $value): ?string
    {
        $normalized = trim(mb_strtolower((string) $value));

        return $normalized === '' ? null : hash('sha256', $normalized);
    }

    private function encryptNullable(?string $value): ?string
    {
        $normalized = trim((string) $value);

        return $normalized === '' ? null : Crypt::encryptString($normalized);
    }

    private function decryptNullable(?string $value): ?string
    {
        return $value === null ? null : Crypt::decryptString($value);
    }
}
