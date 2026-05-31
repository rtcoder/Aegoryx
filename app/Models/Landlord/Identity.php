<?php

namespace App\Models\Landlord;

use App\Modules\Identity\Enums\IdentityStatus;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Hidden(['password'])]
final class Identity extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'status',
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
            'password' => 'hashed',
            'status' => IdentityStatus::class,
        ];
    }
}
