<?php

namespace App\Modules\Entitlements\Actions;

use App\Models\Landlord\AuditLog;
use App\Models\Landlord\Feature;
use App\Models\Landlord\Identity;
use App\Modules\Audit\Enums\AuditLogAction;
use App\Modules\Entitlements\Enums\FeatureStatus;
use Illuminate\Support\Facades\DB;

final readonly class CreateFeatureAction
{
    public function handle(
        string $key,
        string $name,
        ?string $description,
        FeatureStatus $status,
        Identity $actor,
        ?string $ip,
        ?string $userAgent,
    ): Feature {
        return DB::transaction(function () use ($key, $name, $description, $status, $actor, $ip, $userAgent): Feature {
            $feature = Feature::query()->create([
                'key' => $key,
                'name' => $name,
                'description' => $description,
                'status' => $status,
                'created_by' => $actor->id,
                'updated_by' => $actor->id,
            ]);

            AuditLog::query()->create([
                'actor_type' => 'superadmin',
                'actor_id' => $actor->id,
                'subject_type' => Feature::class,
                'subject_id' => $feature->id,
                'action' => AuditLogAction::FeatureCreated,
                'description' => __('audit.feature_created', ['feature' => $feature->key]),
                'before_json' => null,
                'after_json' => [
                    'key' => $feature->key,
                    'name' => $feature->name,
                    'status' => $feature->status->value,
                ],
                'metadata_json' => ['feature_key' => $feature->key],
                'ip' => $ip,
                'user_agent' => $userAgent,
            ]);

            return $feature;
        });
    }
}
