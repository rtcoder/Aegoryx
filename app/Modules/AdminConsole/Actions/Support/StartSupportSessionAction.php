<?php

namespace App\Modules\AdminConsole\Actions\Support;

use App\Models\Landlord\AuditLog;
use App\Models\Landlord\Identity;
use App\Models\Landlord\SupportSession;
use App\Models\Landlord\Tenant;
use App\Modules\AdminConsole\Enums\SupportSessionStatus;
use App\Modules\Audit\Enums\AuditLogAction;
use Illuminate\Support\Facades\DB;

final readonly class StartSupportSessionAction
{
    public function handle(
        Tenant $tenant,
        Identity $actor,
        string $reason,
        int $durationMinutes,
        ?string $ip,
        ?string $userAgent,
    ): SupportSession {
        return DB::transaction(function () use ($tenant, $actor, $reason, $durationMinutes, $ip, $userAgent): SupportSession {
            SupportSession::query()
                ->where('actor_id', $actor->id)
                ->where('status', SupportSessionStatus::Active->value)
                ->update([
                    'status' => SupportSessionStatus::Ended->value,
                    'ended_at' => now(),
                ]);

            $supportSession = SupportSession::query()->create([
                'tenant_id' => $tenant->id,
                'actor_id' => $actor->id,
                'status' => SupportSessionStatus::Active,
                'reason' => $reason,
                'started_at' => now(),
                'expires_at' => now()->addMinutes($durationMinutes),
                'ip' => $ip,
                'user_agent' => $userAgent,
            ]);

            AuditLog::query()->create([
                'actor_type' => 'superadmin',
                'actor_id' => $actor->id,
                'subject_type' => SupportSession::class,
                'subject_id' => $supportSession->id,
                'action' => AuditLogAction::SupportSessionStarted,
                'description' => "Support session [{$supportSession->id}] started for tenant [{$tenant->slug}].",
                'before_json' => null,
                'after_json' => [
                    'status' => SupportSessionStatus::Active->value,
                    'reason' => $reason,
                    'expires_at' => $supportSession->expires_at->toISOString(),
                ],
                'metadata_json' => [
                    'tenant_id' => $tenant->id,
                    'tenant_slug' => $tenant->slug,
                ],
                'ip' => $ip,
                'user_agent' => $userAgent,
            ]);

            return $supportSession->refresh();
        });
    }
}
