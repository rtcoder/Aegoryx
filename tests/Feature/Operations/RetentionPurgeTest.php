<?php

namespace Tests\Feature\Operations;

use App\Models\Landlord\AuditLog;
use App\Models\Landlord\Tenant;
use App\Models\Tenant\ActivityEntry;
use App\Models\Tenant\TenantFile;
use App\Models\Tenant\User;
use App\Modules\Audit\Enums\ActivityEntryAction;
use App\Modules\Audit\Enums\AuditLogAction;
use App\Modules\Files\Enums\FileVisibility;
use App\Modules\Tenancy\Enums\TenantBillingModel;
use App\Modules\Tenancy\Enums\TenantDeploymentType;
use App\Modules\Tenancy\Enums\TenantLicenseType;
use App\Modules\Tenancy\Enums\TenantStatus;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class RetentionPurgeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh', [
            '--database' => 'sqlite',
            '--path' => 'database/migrations/landlord',
        ]);

        Artisan::call('migrate', [
            '--database' => 'sqlite',
            '--path' => 'database/migrations/tenant',
        ]);

        Storage::fake('local');
    }

    public function test_retention_purge_deletes_expired_data(): void
    {
        config()->set('aegoryx.retention.activity_entries_days', 10);
        config()->set('aegoryx.retention.audit_logs_days', 20);
        config()->set('aegoryx.retention.deleted_files_days', 5);

        $this->tenant();
        $user = User::query()->create([
            'name' => 'Owner',
            'email' => 'owner@example.test',
            'password' => 'secret-password',
        ]);

        $auditLog = AuditLog::query()->create([
            'actor_type' => 'system',
            'subject_type' => Tenant::class,
            'action' => AuditLogAction::TenantStatusChanged,
        ]);
        $auditLog->forceFill([
            'created_at' => now()->subDays(30),
            'updated_at' => now()->subDays(30),
        ])->save();

        $activityEntry = ActivityEntry::query()->create([
            'actor_type' => User::class,
            'actor_id' => $user->id,
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'action' => ActivityEntryAction::TenantTwoFactorEnabled,
        ]);
        $activityEntry->forceFill([
            'created_at' => now()->subDays(30),
            'updated_at' => now()->subDays(30),
        ])->save();

        Storage::disk('local')->put('expired-export.json', '{}');
        TenantFile::query()->create([
            'disk' => 'local',
            'path' => 'expired-export.json',
            'original_name' => 'expired-export.json',
            'mime_type' => 'application/json',
            'size_bytes' => 2,
            'checksum_sha256' => hash('sha256', '{}'),
            'visibility' => FileVisibility::Private,
            'expires_at' => now()->subHour(),
            'owner_id' => $user->id,
        ]);

        Storage::disk('local')->put('deleted.txt', 'deleted');
        $deletedFile = TenantFile::query()->create([
            'disk' => 'local',
            'path' => 'deleted.txt',
            'original_name' => 'deleted.txt',
            'mime_type' => 'text/plain',
            'size_bytes' => 7,
            'checksum_sha256' => hash('sha256', 'deleted'),
            'visibility' => FileVisibility::Private,
            'owner_id' => $user->id,
        ]);
        $deletedFile->delete();
        $deletedFile->forceFill(['deleted_at' => now()->subDays(10)])->save();

        $exitCode = Artisan::call('aegoryx:retention:purge');

        $this->assertSame(0, $exitCode);
        $this->assertSame(0, AuditLog::query()->count());
        $this->assertSame(0, ActivityEntry::query()->count());
        $this->assertSame(0, TenantFile::withTrashed()->count());
        Storage::disk('local')->assertMissing('expired-export.json');
        Storage::disk('local')->assertMissing('deleted.txt');
    }

    private function tenant(): Tenant
    {
        return Tenant::query()->create([
            'name' => 'Acme Tenant',
            'slug' => 'acme',
            'schema_name' => 'tenant_acme',
            'status' => TenantStatus::Active,
            'deployment_type' => TenantDeploymentType::Saas,
            'billing_model' => TenantBillingModel::Subscription,
            'license_type' => TenantLicenseType::SaasSubscription,
        ]);
    }
}
