<?php

namespace App\Modules\Cms\Actions;

use App\Models\Tenant\CmsPage;
use App\Models\Tenant\User;
use App\Modules\Audit\Enums\ActivityEntryAction;
use App\Modules\Audit\Services\ActivityLogger;
use App\Modules\Cms\Enums\CmsPageStatus;
use Illuminate\Support\Facades\DB;

final readonly class UnpublishPageAction
{
    public function __construct(
        private ActivityLogger $activity,
    ) {}

    public function handle(CmsPage $page, User $actor): CmsPage
    {
        return DB::transaction(function () use ($page, $actor): CmsPage {
            $before = [
                'status' => $page->status->value,
                'published_at' => $page->published_at?->toISOString(),
            ];

            $page->publishedSnapshot()->delete();

            $page->forceFill([
                'status' => CmsPageStatus::Draft,
                'published_at' => null,
                'published_by' => null,
                'updated_by' => $actor->id,
            ])->save();

            $this->activity->record(
                actorType: User::class,
                actorId: $actor->id,
                subject: $page,
                action: ActivityEntryAction::CmsPageUnpublished,
                description: __('activity.cms_page_unpublished', ['page' => $page->slug]),
                before: $before,
                after: [
                    'status' => CmsPageStatus::Draft->value,
                    'published_at' => null,
                ],
            );

            return $page->refresh();
        });
    }
}
