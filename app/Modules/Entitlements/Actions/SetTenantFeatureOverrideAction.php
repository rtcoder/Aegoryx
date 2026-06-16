<?php

namespace App\Modules\Entitlements\Actions;

use App\Models\Landlord\AuditLog;
use App\Models\Landlord\Identity;
use App\Models\Landlord\Tenant;
use App\Models\Landlord\TenantFeature;
use App\Modules\Audit\Enums\AuditLogAction;
use App\Modules\Entitlements\Enums\SystemFeature;
use App\Modules\Entitlements\Enums\TenantFeatureSource;
use Illuminate\Support\Facades\DB;

final readonly class SetTenantFeatureOverrideAction
{
    public function handle(
        Tenant $tenant,
        SystemFeature $feature,
        bool $enabled,
        string $reason,
        Identity $actor,
        ?string $ip,
        ?string $userAgent,
    ): TenantFeature {
        return DB::transaction(function () use ($tenant, $feature, $enabled, $reason, $actor, $ip, $userAgent): TenantFeature {
            $override = TenantFeature::query()->firstOrNew([
                'tenant_id' => $tenant->id,
                'feature' => $feature->value,
                'source' => TenantFeatureSource::Manual->value,
            ]);

            $before = $override->exists ? [
                'enabled' => $override->enabled,
                'reason' => $override->reason,
                'source' => $override->source->value,
            ] : null;

            $override->forceFill([
                'enabled' => $enabled,
                'reason' => $reason,
                'created_by' => $override->exists ? $override->created_by : $actor->id,
                'updated_by' => $actor->id,
            ])->save();

            AuditLog::query()->create([
                'actor_type' => 'superadmin',
                'actor_id' => $actor->id,
                'subject_type' => TenantFeature::class,
                'subject_id' => $override->id,
                'action' => AuditLogAction::TenantFeatureOverrideSet,
                'description' => __('audit.manual_feature_override_set', [
                    'feature' => $feature->value,
                    'tenant' => $tenant->slug,
                    'state' => $enabled ? __('audit.state_enabled') : __('audit.state_disabled'),
                ]),
                'before_json' => $before,
                'after_json' => [
                    'enabled' => $enabled,
                    'reason' => $reason,
                    'source' => TenantFeatureSource::Manual->value,
                ],
                'metadata_json' => [
                    'feature_key' => $feature->value,
                    'tenant_slug' => $tenant->slug,
                ],
                'ip' => $ip,
                'user_agent' => $userAgent,
            ]);

            return $override->refresh();
        });
    }
}
