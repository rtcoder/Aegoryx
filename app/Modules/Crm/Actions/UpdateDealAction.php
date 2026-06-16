<?php

namespace App\Modules\Crm\Actions;

use App\Models\Tenant\CrmDeal;
use App\Models\Tenant\User;
use App\Modules\Audit\Enums\ActivityEntryAction;
use App\Modules\Audit\Services\ActivityLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

final readonly class UpdateDealAction
{
    public function __construct(
        private ActivityLogger $activity,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(CrmDeal $deal, array $data, User $actor): CrmDeal
    {
        Gate::forUser($actor)->authorize('update', $deal);

        return DB::transaction(function () use ($deal, $data, $actor): CrmDeal {
            $before = $this->activityPayload($deal);

            $deal->forceFill([
                ...$data,
                'updated_by' => $actor->id,
            ])->save();

            $this->activity->record(
                actorType: User::class,
                actorId: $actor->id,
                subject: $deal,
                action: ActivityEntryAction::CrmDealUpdated,
                description: __('activity.crm_deal_updated', ['deal' => $deal->title]),
                before: $before,
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
