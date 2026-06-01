<?php

namespace App\Models\Landlord;

use App\Modules\Identity\Enums\IdentityStatus;
use App\Support\Localization\Locale;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string|null $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string|null $password
 * @property IdentityStatus $status
 * @property Locale $locale
 * @property bool $is_super_admin
 * @property string|null $remember_token
 * @property Carbon|null $last_login_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
#[Fillable([
    'name',
    'email',
    'email_verified_at',
    'password',
    'status',
    'locale',
    'is_super_admin',
    'last_login_at',
    'created_by',
    'updated_by',
    'deleted_by',
])]
#[Hidden(['password'])]
final class Identity extends Authenticatable
{
    use Notifiable, SoftDeletes;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_super_admin' => 'boolean',
            'last_login_at' => 'datetime',
            'locale' => Locale::class,
            'password' => 'hashed',
            'status' => IdentityStatus::class,
        ];
    }
}
