<?php

namespace App\Modules\Crm\Actions;

use App\Models\Tenant\CrmDeal;
use App\Models\Tenant\User;
use App\Modules\Audit\Enums\ActivityEntryAction;
use App\Modules\Audit\Services\ActivityLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

final readonly class DeleteDealAction
{
    public function __construct(
        private ActivityLogger $activity,
    ) {}

    public function handle(CrmDeal $deal, User $actor): void
    {
        Gate::forUser($actor)->authorize('delete', $deal);

        DB::transaction(function () use ($deal, $actor): void {
            $before = [
                'title' => $deal->title,
                'company_id' => $deal->company_id,
                'contact_id' => $deal->contact_id,
                'status' => $deal->status->value,
                'value_amount' => $deal->value_amount,
                'currency' => $deal->currency,
                'expected_close_date' => $deal->expected_close_date?->toDateString(),
            ];

            $deal->forceFill([
                'deleted_by' => $actor->id,
            ])->save();

            $deal->delete();

            $this->activity->record(
                actorType: User::class,
                actorId: $actor->id,
                subject: $deal,
                action: ActivityEntryAction::CrmDealDeleted,
                description: __('activity.crm_deal_deleted', ['deal' => $deal->title]),
                before: $before,
            );
        });
    }
}
