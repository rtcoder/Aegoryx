<?php

namespace App\Models\Tenant;

use App\Modules\Identity\Enums\TenantUserRole;
use App\Support\Localization\Locale;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property Locale $locale
 * @property TenantUserRole $role
 * @property string|null $remember_token
 * @property string|null $two_factor_secret
 * @property array<int, string>|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
#[Fillable(['name', 'email', 'password', 'locale', 'role'])]
#[Hidden(['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'])]
final class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    protected static function booted(): void
    {
        self::creating(function (User $user): void {
            if ($user->locale !== null) {
                return;
            }

            $tenant = request()->attributes->get('tenant');

            $user->locale = $tenant?->locale ?? Locale::from(config('aegoryx.localization.default_locale', 'pl'));
        });

        self::creating(function (User $user): void {
            $user->role ??= TenantUserRole::Member;
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'locale' => Locale::class,
            'role' => TenantUserRole::class,
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'two_factor_recovery_codes' => 'encrypted:array',
            'two_factor_secret' => 'encrypted',
        ];
    }

    public function hasTwoFactorEnabled(): bool
    {
        return $this->two_factor_secret !== null
            && $this->two_factor_confirmed_at !== null;
    }

    public function hasTenantRole(TenantUserRole ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function canManageTenantUsers(): bool
    {
        return $this->role->canManageUsers();
    }

    public function canManageTenantContent(): bool
    {
        return $this->role->canManageContent();
    }

    public function canManageTenantCrm(): bool
    {
        return $this->role->canManageCrm();
    }

    public function canManageTenantFiles(): bool
    {
        return $this->role->canManageFiles();
    }

    public function canExportTenantActivity(): bool
    {
        return $this->role->canExportActivity();
    }
}
