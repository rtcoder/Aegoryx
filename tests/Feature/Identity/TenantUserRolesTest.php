<?php

namespace Tests\Feature\Identity;

use App\Models\Tenant\ActivityEntry;
use App\Models\Tenant\CmsPage;
use App\Models\Tenant\CrmContact;
use App\Models\Tenant\TenantFile;
use App\Models\Tenant\User;
use App\Modules\Audit\Enums\ActivityEntryAction;
use App\Modules\Cms\Enums\CmsPageStatus;
use App\Modules\Files\Enums\FileVisibility;
use App\Modules\Identity\Enums\TenantUserRole;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

final class TenantUserRolesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh', [
            '--database' => 'sqlite',
            '--path' => 'database/migrations/tenant',
        ]);
    }

    public function test_new_tenant_user_defaults_to_member_role(): void
    {
        $user = User::query()->create([
            'name' => 'Member',
            'email' => 'member@example.test',
            'password' => 'secret-password',
        ]);

        $this->assertSame(TenantUserRole::Member, $user->role);
        $this->assertTrue($user->canManageTenantCrm());
        $this->assertFalse($user->canExportTenantActivity());
    }

    public function test_viewer_can_read_but_cannot_modify_crm_or_cms(): void
    {
        $viewer = $this->user(TenantUserRole::Viewer);
        $contact = CrmContact::query()->create([
            'first_name' => 'Ada',
        ]);
        $page = CmsPage::query()->create([
            'title' => 'Homepage',
            'slug' => 'home',
            'status' => CmsPageStatus::Draft,
            'draft_content' => ['schema_version' => 1, 'blocks' => []],
        ]);

        $this->assertTrue(Gate::forUser($viewer)->allows('viewAny', CrmContact::class));
        $this->assertTrue(Gate::forUser($viewer)->allows('view', $contact));
        $this->assertFalse(Gate::forUser($viewer)->allows('create', CrmContact::class));
        $this->assertFalse(Gate::forUser($viewer)->allows('update', $contact));
        $this->assertTrue(Gate::forUser($viewer)->allows('view', $page));
        $this->assertFalse(Gate::forUser($viewer)->allows('create', CmsPage::class));
        $this->assertFalse(Gate::forUser($viewer)->allows('publish', $page));
    }

    public function test_member_can_manage_work_modules_but_cannot_export_activity(): void
    {
        $member = $this->user(TenantUserRole::Member);
        $contact = CrmContact::query()->create([
            'first_name' => 'Ada',
        ]);
        $page = CmsPage::query()->create([
            'title' => 'Homepage',
            'slug' => 'home',
            'status' => CmsPageStatus::Draft,
            'draft_content' => ['schema_version' => 1, 'blocks' => []],
        ]);

        $this->assertTrue(Gate::forUser($member)->allows('create', CrmContact::class));
        $this->assertTrue(Gate::forUser($member)->allows('update', $contact));
        $this->assertTrue(Gate::forUser($member)->allows('create', CmsPage::class));
        $this->assertTrue(Gate::forUser($member)->allows('publish', $page));
        $this->assertFalse(Gate::forUser($member)->allows('export', ActivityEntry::class));
    }

    public function test_file_management_and_activity_export_respect_roles(): void
    {
        $owner = $this->user(TenantUserRole::Owner);
        $viewer = $this->user(TenantUserRole::Viewer, 'viewer@example.test');
        $file = TenantFile::query()->create([
            'disk' => 'local',
            'path' => 'tenant/acme/report.txt',
            'original_name' => 'report.txt',
            'size_bytes' => 12,
            'visibility' => FileVisibility::Private,
            'owner_id' => $owner->id,
        ]);

        ActivityEntry::query()->create([
            'actor_type' => User::class,
            'actor_id' => $owner->id,
            'subject_type' => TenantFile::class,
            'subject_id' => $file->id,
            'action' => ActivityEntryAction::FileRegistered,
            'description' => 'File registered.',
        ]);

        $this->assertTrue(Gate::forUser($owner)->allows('create', TenantFile::class));
        $this->assertTrue(Gate::forUser($owner)->allows('delete', $file));
        $this->assertTrue(Gate::forUser($owner)->allows('export', ActivityEntry::class));
        $this->assertFalse(Gate::forUser($viewer)->allows('create', TenantFile::class));
        $this->assertFalse(Gate::forUser($viewer)->allows('delete', $file));
        $this->assertFalse(Gate::forUser($viewer)->allows('export', ActivityEntry::class));
    }

    private function user(TenantUserRole $role, string $email = 'user@example.test'): User
    {
        return User::query()->create([
            'name' => ucfirst($role->value),
            'email' => $email,
            'password' => 'secret-password',
            'role' => $role,
        ]);
    }
}
