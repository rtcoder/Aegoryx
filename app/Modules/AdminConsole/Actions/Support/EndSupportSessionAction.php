<?php

namespace App\Modules\AdminConsole\Actions\Support;

use App\Models\Landlord\AuditLog;
use App\Models\Landlord\Identity;
use App\Models\Landlord\SupportSession;
use App\Modules\AdminConsole\Enums\SupportSessionStatus;
use Illuminate\Support\Facades\DB;

final readonly class EndSupportSessionAction
{
    public function handle(
        SupportSession $supportSession,
        Identity $actor,
        string $status,
        ?string $ip,
        ?string $userAgent,
    ): SupportSession {
        return DB::transaction(function () use ($supportSession, $actor, $status, $ip, $userAgent): SupportSession {
            $before = [
                'status' => $supportSession->status->value,
                'ended_at' => $supportSession->ended_at?->toISOString(),
            ];

            $supportSession->forceFill([
                'status' => SupportSessionStatus::from($status),
                'ended_at' => now(),
            ])->save();

            AuditLog::query()->create([
                'actor_type' => 'superadmin',
                'actor_id' => $actor->id,
                'subject_type' => SupportSession::class,
                'subject_id' => $supportSession->id,
                'action' => $status === SupportSessionStatus::Expired->value ? 'support_session_expired' : 'support_session_ended',
                'description' => "Support session [{$supportSession->id}] marked as [{$status}].",
                'before_json' => $before,
                'after_json' => [
                    'status' => $status,
                    'ended_at' => $supportSession->ended_at?->toISOString(),
                ],
                'metadata_json' => [
                    'tenant_id' => $supportSession->tenant_id,
                ],
                'ip' => $ip,
                'user_agent' => $userAgent,
            ]);

            return $supportSession->refresh();
        });
    }
}
