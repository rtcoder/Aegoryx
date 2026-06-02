<?php

namespace App\Modules\Crm\Actions;

use App\Models\Tenant\CrmContact;
use App\Models\Tenant\User;
use App\Modules\Audit\Enums\ActivityEntryAction;
use App\Modules\Audit\Services\ActivityLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

final readonly class DeleteContactAction
{
    public function __construct(
        private ActivityLogger $activity,
    ) {}

    public function handle(CrmContact $contact, User $actor): void
    {
        Gate::forUser($actor)->authorize('delete', $contact);

        DB::transaction(function () use ($contact, $actor): void {
            $contact->forceFill([
                'deleted_by' => $actor->id,
            ])->save();

            $contact->delete();

            $this->activity->record(
                actorType: User::class,
                actorId: $actor->id,
                subject: $contact,
                action: ActivityEntryAction::CrmContactDeleted,
                description: __('activity.crm_contact_deleted', ['contact' => $contact->first_name]),
                before: [
                    'first_name' => $contact->first_name,
                    'last_name' => $contact->last_name,
                    'email' => $contact->email,
                    'phone' => $contact->phone,
                    'position' => $contact->position,
                ],
            );
        });
    }
}
