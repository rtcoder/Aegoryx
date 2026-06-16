<?php

namespace App\Modules\Crm\Actions;

use App\Models\Tenant\CrmDeal;
use App\Models\Tenant\User;
use App\Modules\Audit\Enums\ActivityEntryAction;
use App\Modules\Audit\Services\ActivityLogger;
use App\Modules\Crm\Enums\CrmDealStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

final readonly class CreateDealAction
{
    public function __construct(
        private ActivityLogger $activity,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data, User $actor): CrmDeal
    {
        Gate::forUser($actor)->authorize('create', CrmDeal::class);

        return DB::transaction(function () use ($data, $actor): CrmDeal {
            $deal = CrmDeal::query()->create([
                ...$data,
                'status' => $data['status'] ?? CrmDealStatus::Open,
                'created_by' => $actor->id,
                'updated_by' => $actor->id,
            ]);

            $this->activity->record(
                actorType: User::class,
                actorId: $actor->id,
                subject: $deal,
                action: ActivityEntryAction::CrmDealCreated,
                description: __('activity.crm_deal_created', ['deal' => $deal->title]),
                after: $this->activityPayload($deal->refresh()),
            );

            return $deal->load(['company', 'contact']);
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function activityPayload(CrmDeal $deal): array
    {
        return [
            'title' => $deal->title,
            'company_id' => $deal->company_id,
            'contact_id' => $deal->contact_id,
            'status' => $deal->status->value,
            'value_amount' => $deal->value_amount,
            'currency' => $deal->currency,
            'expected_close_date' => $deal->expected_close_date?->toDateString(),
        ];
    }
}
