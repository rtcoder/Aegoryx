<?php

namespace App\Modules\Cms\Actions;

use App\Models\Tenant\CmsPage;
use App\Models\Tenant\User;
use App\Modules\Audit\Enums\ActivityEntryAction;
use App\Modules\Audit\Services\ActivityLogger;
use App\Modules\Cms\Enums\CmsPageStatus;
use Illuminate\Support\Facades\DB;

final readonly class PublishPageAction
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

            $page->forceFill([
                'status' => CmsPageStatus::Published,
                'published_at' => now(),
                'published_by' => $actor->id,
                'updated_by' => $actor->id,
            ])->save();

            $page->publishedSnapshot()->updateOrCreate(
                ['cms_page_id' => $page->id],
                [
                    'title' => $page->title,
                    'slug' => $page->slug,
                    'content' => $page->draft_content,
                    'published_at' => $page->published_at,
                    'published_by' => $actor->id,
                ],
            );

            $this->activity->record(
                actorType: User::class,
                actorId: $actor->id,
                subject: $page,
                action: ActivityEntryAction::CmsPagePublished,
                description: "CMS page [{$page->slug}] published.",
                before: $before,
                after: [
                    'status' => CmsPageStatus::Published->value,
                    'published_at' => $page->published_at?->toISOString(),
                ],
            );

            return $page->refresh();
        });
    }
}
