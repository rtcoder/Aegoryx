<?php

namespace App\Models\Landlord;

use App\Modules\Identity\Enums\IdentityStatus;
use App\Support\Localization\Locale;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Hidden(['password'])]
final class Identity extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $fillable = [
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
    ];

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
