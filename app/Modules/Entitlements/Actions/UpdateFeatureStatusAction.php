<?php

namespace App\Modules\Entitlements\Actions;

use App\Models\Landlord\AuditLog;
use App\Models\Landlord\Feature;
use App\Models\Landlord\Identity;
use App\Modules\Audit\Enums\AuditLogAction;
use App\Modules\Entitlements\Enums\FeatureStatus;
use Illuminate\Support\Facades\DB;

final readonly class UpdateFeatureStatusAction
{
    public function handle(
        Feature $feature,
        FeatureStatus $status,
        Identity $actor,
        ?string $ip,
        ?string $userAgent,
    ): Feature {
        return DB::transaction(function () use ($feature, $status, $actor, $ip, $userAgent): Feature {
            $before = [
                'status' => $feature->status->value,
            ];

            $feature->forceFill([
                'status' => $status,
                'updated_by' => $actor->id,
            ])->save();

            AuditLog::query()->create([
                'actor_type' => 'superadmin',
                'actor_id' => $actor->id,
                'subject_type' => Feature::class,
                'subject_id' => $feature->id,
                'action' => AuditLogAction::FeatureStatusChanged,
                'description' => __('audit.feature_status_changed', ['feature' => $feature->key, 'status' => $status->value]),
                'before_json' => $before,
                'after_json' => ['status' => $status->value],
                'metadata_json' => ['feature_key' => $feature->key],
                'ip' => $ip,
                'user_agent' => $userAgent,
            ]);

            return $feature->refresh();
        });
    }
}
