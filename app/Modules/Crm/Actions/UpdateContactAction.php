<?php

namespace App\Modules\Crm\Actions;

use App\Models\Tenant\CrmContact;
use App\Models\Tenant\User;
use App\Modules\Audit\Enums\ActivityEntryAction;
use App\Modules\Audit\Services\ActivityLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

final readonly class UpdateContactAction
{
    public function __construct(
        private ActivityLogger $activity,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(CrmContact $contact, array $data, User $actor): CrmContact
    {
        Gate::forUser($actor)->authorize('update', $contact);

        return DB::transaction(function () use ($contact, $data, $actor): CrmContact {
            $before = $this->activityPayload($contact);

            $contact->forceFill([
                ...$data,
                'updated_by' => $actor->id,
            ])->save();

            $this->activity->record(
                actorType: User::class,
                actorId: $actor->id,
                subject: $contact,
                action: ActivityEntryAction::CrmContactUpdated,
                description: __('activity.crm_contact_updated', ['contact' => $contact->first_name]),
                before: $before,
                after: $this->activityPayload($contact->refresh()),
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
