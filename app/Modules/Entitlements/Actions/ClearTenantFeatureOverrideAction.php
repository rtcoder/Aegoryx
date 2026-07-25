<?php

namespace App\Modules\Entitlements\Actions;

use App\Models\System\AuditLog;
use App\Models\System\Identity;
use App\Models\System\Tenant;
use App\Models\System\TenantFeature;
use App\Modules\Audit\Enums\AuditLogAction;
use App\Modules\Entitlements\Enums\SystemFeature;
use App\Modules\Entitlements\Enums\TenantFeatureSource;
use Illuminate\Support\Facades\DB;

final readonly class ClearTenantFeatureOverrideAction
{
    public function handle(
        Tenant $tenant,
        SystemFeature $feature,
        Identity $actor,
        ?string $ip,
        ?string $userAgent,
    ): void {
        DB::transaction(function () use ($tenant, $feature, $actor, $ip, $userAgent): void {
            $override = TenantFeature::query()
                ->where('tenant_id', $tenant->id)
                ->where('feature', $feature->value)
                ->where('source', TenantFeatureSource::Manual->value)
                ->first();

            if (! $override) {
                return;
            }

            $before = [
                'enabled' => $override->enabled,
                'reason' => $override->reason,
                'source' => $override->source->value,
            ];
            $overrideId = $override->id;

            $override->delete();

            AuditLog::query()->create([
                'actor_type' => 'superadmin',
                'actor_id' => $actor->id,
                'subject_type' => TenantFeature::class,
                'subject_id' => $overrideId,
                'action' => AuditLogAction::TenantFeatureOverrideCleared,
                'description' => __('audit.manual_feature_override_cleared', [
                    'feature' => $feature->value,
                    'tenant' => $tenant->slug,
                ]),
                'before_json' => $before,
                'after_json' => null,
                'metadata_json' => [
                    'feature_key' => $feature->value,
                    'tenant_slug' => $tenant->slug,
                ],
                'ip' => $ip,
                'user_agent' => $userAgent,
            ]);
        });
    }
}
