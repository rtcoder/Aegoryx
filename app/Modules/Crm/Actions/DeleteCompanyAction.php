<?php

namespace App\Modules\Crm\Actions;

use App\Models\Tenant\CrmCompany;
use App\Models\Tenant\User;
use App\Modules\Audit\Enums\ActivityEntryAction;
use App\Modules\Audit\Services\ActivityLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

final readonly class DeleteCompanyAction
{
    public function __construct(
        private ActivityLogger $activity,
    ) {}

    public function handle(CrmCompany $company, User $actor): void
    {
        Gate::forUser($actor)->authorize('delete', $company);

        DB::transaction(function () use ($company, $actor): void {
            $before = [
                'name' => $company->name,
                'website' => $company->website,
                'email' => $company->email,
                'phone' => $company->phone,
                'contact_ids' => $company->contacts()->pluck('crm_contacts.id')->all(),
            ];

            $company->forceFill([
                'deleted_by' => $actor->id,
            ])->save();

            $company->delete();

            $this->activity->record(
                actorType: User::class,
                actorId: $actor->id,
                subject: $company,
                action: ActivityEntryAction::CrmCompanyDeleted,
                description: __('activity.crm_company_deleted', ['company' => $company->name]),
                before: $before,
            );
        });
    }
}
