<?php

namespace App\Modules\Crm\Actions;

use App\Models\Tenant\CrmCompany;
use App\Models\Tenant\User;
use App\Modules\Audit\Enums\ActivityEntryAction;
use App\Modules\Audit\Services\ActivityLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

final readonly class CreateCompanyAction
{
    public function __construct(
        private ActivityLogger $activity,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data, User $actor): CrmCompany
    {
        Gate::forUser($actor)->authorize('create', CrmCompany::class);

        return DB::transaction(function () use ($data, $actor): CrmCompany {
            $contactIds = $data['contact_ids'] ?? [];
            unset($data['contact_ids']);

            $company = CrmCompany::query()->create([
                ...$data,
                'created_by' => $actor->id,
                'updated_by' => $actor->id,
            ]);

            $company->contacts()->sync($contactIds);

            $this->activity->record(
                actorType: User::class,
                actorId: $actor->id,
                subject: $company,
                action: ActivityEntryAction::CrmCompanyCreated,
                description: __('activity.crm_company_created', ['company' => $company->name]),
                after: $this->activityPayload($company->refresh()),
            );

            return $company->load('contacts');
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function activityPayload(CrmCompany $company): array
    {
        return [
            'name' => $company->name,
            'website' => $company->website,
            'email' => $company->email,
            'phone' => $company->phone,
            'contact_ids' => $company->contacts()->pluck('crm_contacts.id')->all(),
        ];
    }
}
