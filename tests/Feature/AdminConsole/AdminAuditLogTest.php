<?php

namespace Tests\Feature\AdminConsole;

use App\Models\System\AuditLog;
use App\Models\System\Identity;
use App\Models\System\Tenant;
use App\Modules\Audit\Enums\AuditLogAction;
use App\Modules\Identity\Enums\IdentityStatus;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

final class AdminAuditLogTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh', [
            '--database' => 'sqlite',
            '--path' => 'database/migrations/system',
        ]);
    }

    public function test_superadmin_can_view_audit_log_entries(): void
    {
        $superadmin = $this->superadmin();
        $this->actingAs($superadmin, 'admin');

        AuditLog::query()->create([
            'actor_type' => 'superadmin',
            'actor_id' => $superadmin->id,
            'subject_type' => Tenant::class,
            'subject_id' => 10,
            'action' => AuditLogAction::TenantStatusChanged,
            'description' => 'Tenant status changed from active to suspended.',
            'ip' => '127.0.0.1',
        ]);

        $this
            ->get('http://admin.aegoryx.test/audit')
            ->assertOk()
            ->assertSee(__('audit_view.audit_title'))
            ->assertSee(AuditLogAction::TenantStatusChanged->value)
            ->assertSee('Tenant status changed from active to suspended.')
            ->assertSee('127.0.0.1');
    }

    private function superadmin(): Identity
    {
        return Identity::query()->create([
            'email' => 'admin@example.test',
            'is_super_admin' => true,
            'status' => IdentityStatus::Active,
        ]);
    }
}
