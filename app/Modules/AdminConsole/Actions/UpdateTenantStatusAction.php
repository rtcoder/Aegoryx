<?php

namespace App\Modules\AdminConsole\Actions;

use App\Models\Landlord\AuditLog;
use App\Models\Landlord\Identity;
use App\Models\Landlord\Tenant;
use App\Modules\Tenancy\Enums\TenantStatus;
use Illuminate\Support\Facades\DB;

final readonly class UpdateTenantStatusAction
{
    public function handle(
        Tenant $tenant,
        TenantStatus $status,
        Identity $actor,
        ?string $ip,
        ?string $userAgent,
    ): Tenant {
        return DB::transaction(function () use ($tenant, $status, $actor, $ip, $userAgent): Tenant {
            $before = [
                'status' => $tenant->status->value,
            ];

            $tenant->forceFill([
                'status' => $status,
                'updated_by' => $actor->id,
            ])->save();

            AuditLog::query()->create([
                'actor_type' => 'superadmin',
                'actor_id' => $actor->id,
                'subject_type' => Tenant::class,
                'subject_id' => $tenant->id,
                'action' => 'tenant_status_changed',
                'description' => "Tenant [{$tenant->slug}] status changed to [{$status->value}].",
                'before_json' => $before,
                'after_json' => ['status' => $status->value],
                'metadata_json' => ['tenant_slug' => $tenant->slug],
                'ip' => $ip,
                'user_agent' => $userAgent,
            ]);

            return $tenant->refresh();
        });
    }
}
