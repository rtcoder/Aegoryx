<?php

namespace App\Modules\Crm\Actions;

use App\Models\Tenant\CrmContact;
use App\Models\Tenant\User;
use App\Modules\Audit\Enums\ActivityEntryAction;
use App\Modules\Audit\Services\ActivityLogger;
use App\Modules\Entitlements\Services\EntitlementLimitEnforcer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

final readonly class CreateContactAction
{
    public function __construct(
        private ActivityLogger $activity,
        private EntitlementLimitEnforcer $limits,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data, User $actor): CrmContact
    {
        Gate::forUser($actor)->authorize('create', CrmContact::class);
        $this->limits->assertCanCreateCrmContact();

        return DB::transaction(function () use ($data, $actor): CrmContact {
            $contact = CrmContact::query()->create([
                ...$data,
                'created_by' => $actor->id,
                'updated_by' => $actor->id,
            ]);

            $this->activity->record(
                actorType: User::class,
                actorId: $actor->id,
                subject: $contact,
                action: ActivityEntryAction::CrmContactCreated,
                description: __('activity.crm_contact_created', ['contact' => $contact->first_name]),
                after: $this->activityPayload($contact),
            );

            return $contact->refresh();
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function activityPayload(CrmContact $contact): array
    {
        return [
            'first_name' => $contact->first_name,
            'last_name' => $contact->last_name,
            'email' => $contact->email,
            'phone' => $contact->phone,
            'position' => $contact->position,
        ];
    }
}
