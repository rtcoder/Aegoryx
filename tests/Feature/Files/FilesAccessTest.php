<?php

namespace Tests\Feature\Files;

use App\Models\Landlord\Tenant;
use App\Models\Landlord\TenantDomain;
use App\Models\Landlord\TenantFeature;
use App\Models\Tenant\ActivityEntry;
use App\Models\Tenant\TenantFile;
use App\Models\Tenant\User;
use App\Modules\Audit\Enums\ActivityEntryAction;
use App\Modules\Entitlements\Enums\TenantFeatureSource;
use App\Modules\Files\Actions\DeleteFileMetadataAction;
use App\Modules\Files\Actions\RegisterFileMetadataAction;
use App\Modules\Files\Enums\FileVisibility;
use App\Modules\Identity\Enums\TenantUserRole;
use App\Modules\Tenancy\Enums\TenantBillingModel;
use App\Modules\Tenancy\Enums\TenantDeploymentType;
use App\Modules\Tenancy\Enums\TenantDomainStatus;
use App\Modules\Tenancy\Enums\TenantDomainType;
use App\Modules\Tenancy\Enums\TenantLicenseType;
use App\Modules\Tenancy\Enums\TenantStatus;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class FilesAccessTest extends TestCase
{
    private Tenant $tenant;

    private User $owner;

    private User $otherUser;

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

        $this->tenant = $this->tenant();
        $this->domain($this->tenant);
        $this->manualOverride($this->tenant, 'files', true);

        $this->owner = User::query()->create([
            'name' => 'Owner',
            'email' => 'owner@example.test',
            'password' => 'secret-password',
            'role' => TenantUserRole::Owner,
        ]);

        $this->otherUser = User::query()->create([
            'name' => 'Other User',
            'email' => 'other@example.test',
            'password' => 'secret-password',
        ]);
    }

    public function test_file_metadata_can_be_registered_from_storage(): void
    {
        Storage::disk('local')->put('tenant/acme/report.txt', 'Quarterly report');

        $file = app(RegisterFileMetadataAction::class)->handle(
            disk: 'local',
            path: 'tenant/acme/report.txt',
            originalName: 'report.txt',
            mimeType: 'text/plain',
            ownerId: $this->owner->id,
            actor: $this->owner,
        );

        $activity = ActivityEntry::query()->where('action', ActivityEntryAction::FileRegistered)->firstOrFail();

        $this->assertSame('local', $file->disk);
        $this->assertSame('tenant/acme/report.txt', $file->path);
        $this->assertSame('report.txt', $file->original_name);
        $this->assertSame(strlen('Quarterly report'), $file->size_bytes);
        $this->assertSame(hash('sha256', 'Quarterly report'), $file->checksum_sha256);
        $this->assertSame(FileVisibility::Private, $file->visibility);
        $this->assertSame($this->owner->id, $activity->actor_id);
        $this->assertSame(TenantFile::class, $activity->subject_type);
    }

    public function test_owner_can_download_private_file_and_activity_is_recorded(): void
    {
        $this->actingAs($this->owner, 'web');
        Storage::disk('local')->put('tenant/acme/report.txt', 'Quarterly report');
        $file = $this->file('tenant/acme/report.txt', $this->owner);

        $this
            ->get("http://acme.aegoryx.test/panel/files/{$file->id}/download")
            ->assertOk()
            ->assertHeader('content-disposition', 'attachment; filename=report.txt');

        $activity = ActivityEntry::query()->where('action', ActivityEntryAction::FileDownloaded)->firstOrFail();

        $this->assertSame($this->owner->id, $activity->actor_id);
        $this->assertSame($file->id, $activity->subject_id);
        $this->assertSame($file->id, $activity->metadata_json['file_id']);
        $this->assertArrayNotHasKey('path', $activity->metadata_json);
    }

    public function test_non_owner_cannot_download_private_file(): void
    {
        $this->actingAs($this->otherUser, 'web');
        Storage::disk('local')->put('tenant/acme/report.txt', 'Quarterly report');
        $file = $this->file('tenant/acme/report.txt', $this->owner);

        $this
            ->get("http://acme.aegoryx.test/panel/files/{$file->id}/download")
            ->assertForbidden();

        $this->assertSame(0, ActivityEntry::query()->where('action', ActivityEntryAction::FileDownloaded)->count());
    }

    public function test_file_metadata_delete_is_soft_delete_and_audited(): void
    {
        Storage::disk('local')->put('tenant/acme/report.txt', 'Quarterly report');
        $file = $this->file('tenant/acme/report.txt', $this->owner);

        app(DeleteFileMetadataAction::class)->handle($file, $this->owner);

        $activity = ActivityEntry::query()->where('action', ActivityEntryAction::FileDeleted)->firstOrFail();

        $this->assertSoftDeleted('files', ['id' => $file->id]);
        $this->assertSame($this->owner->id, $file->refresh()->deleted_by);
        $this->assertSame($file->id, $activity->subject_id);
        Storage::disk('local')->assertExists('tenant/acme/report.txt');
    }

    public function test_files_index_renders_metadata(): void
    {
        $this->actingAs($this->owner, 'web');
        Storage::disk('local')->put('tenant/acme/report.txt', 'Quarterly report');
        $this->file('tenant/acme/report.txt', $this->owner);

        $this
            ->get('http://acme.aegoryx.test/panel/files')
            ->assertOk()
            ->assertSee('Pliki')
            ->assertSee('report.txt')
            ->assertSee('Owner');
    }

    public function test_user_can_upload_private_file_and_metadata_is_registered(): void
    {
        $this->actingAs($this->owner, 'web');

        $this
            ->post('http://acme.aegoryx.test/panel/files', [
                'file' => UploadedFile::fake()->createWithContent('contract.txt', 'Signed contract'),
            ])
            ->assertRedirect('http://acme.aegoryx.test/panel/files')
            ->assertSessionHas('success', __('files.uploaded'));

        $file = TenantFile::query()->firstOrFail();
        $activity = ActivityEntry::query()->where('action', ActivityEntryAction::FileRegistered)->firstOrFail();

        Storage::disk('local')->assertExists($file->path);
        $this->assertSame('contract.txt', $file->original_name);
        $this->assertSame($this->owner->id, $file->owner_id);
        $this->assertSame(FileVisibility::Private, $file->visibility);
        $this->assertSame($file->id, $activity->subject_id);
    }

    public function test_activity_export_creates_expiring_private_file_and_audit_entry(): void
    {
        $this->actingAs($this->owner, 'web');

        ActivityEntry::query()->create([
            'actor_type' => User::class,
            'actor_id' => $this->owner->id,
            'subject_type' => User::class,
            'subject_id' => $this->owner->id,
            'action' => ActivityEntryAction::CrmContactCreated,
            'description' => 'Contact was created.',
            'metadata_json' => ['scope' => 'crm'],
        ]);

        $this
            ->post('http://acme.aegoryx.test/panel/files/exports/activity')
            ->assertRedirect('http://acme.aegoryx.test/panel/files')
            ->assertSessionHas('success', __('flash.activity_export_created'));

        $file = TenantFile::query()
            ->where('original_name', 'activity-export.json')
            ->firstOrFail();

        $payload = json_decode(Storage::disk('local')->get($file->path), true, 512, JSON_THROW_ON_ERROR);
        $activity = ActivityEntry::query()->where('action', ActivityEntryAction::ActivityExportCreated)->firstOrFail();

        $this->assertSame('application/json', $file->mime_type);
        $this->assertSame(FileVisibility::Private, $file->visibility);
        $this->assertSame($this->owner->id, $file->owner_id);
        $this->assertNotNull($file->expires_at);
        $this->assertTrue($file->expires_at->isFuture());
        $this->assertSame(ActivityEntryAction::CrmContactCreated->value, $payload['entries'][0]['action']);
        $this->assertSame($file->id, $activity->subject_id);
        $this->assertSame($file->id, $activity->metadata_json['file_id']);
    }

    private function file(string $path, User $owner): TenantFile
    {
        return TenantFile::query()->create([
            'disk' => 'local',
            'path' => $path,
            'original_name' => 'report.txt',
            'mime_type' => 'text/plain',
            'size_bytes' => Storage::disk('local')->size($path),
            'checksum_sha256' => hash('sha256', Storage::disk('local')->get($path)),
            'visibility' => FileVisibility::Private,
            'owner_id' => $owner->id,
            'created_by' => $owner->id,
            'updated_by' => $owner->id,
        ]);
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

    private function domain(Tenant $tenant): TenantDomain
    {
        return TenantDomain::query()->create([
            'tenant_id' => $tenant->id,
            'domain' => 'acme.aegoryx.test',
            'type' => TenantDomainType::Primary,
            'status' => TenantDomainStatus::Verified,
        ]);
    }

    private function manualOverride(Tenant $tenant, string $featureKey, bool $enabled): TenantFeature
    {
        return TenantFeature::query()->create([
            'tenant_id' => $tenant->id,
            'feature' => $featureKey,
            'enabled' => $enabled,
            'source' => TenantFeatureSource::Manual,
            'reason' => 'Test entitlement.',
        ]);
    }
}
