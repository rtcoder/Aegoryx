<?php

namespace App\Modules\Cms\Actions;

use App\Models\Tenant\CmsPage;
use App\Models\Tenant\User;
use App\Modules\Audit\Enums\ActivityEntryAction;
use App\Modules\Audit\Services\ActivityLogger;
use App\Modules\Cms\Enums\CmsPageStatus;
use App\Modules\PublicApi\Support\PublicApiCacheKeys;
use App\Services\Tenancy\TenancyManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

final readonly class PublishPageAction
{
    public function __construct(
        private ActivityLogger $activity,
        private PublicApiCacheKeys $cacheKeys,
        private TenancyManager $tenancy,
    ) {}

    public function handle(CmsPage $page, User $actor): CmsPage
    {
        Gate::forUser($actor)->authorize('publish', $page);

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

            if ($tenant = $this->tenancy->current()) {
                Cache::forget($this->cacheKeys->publishedPageSlug($tenant, $page->slug));
            }

            $this->activity->record(
                actorType: User::class,
                actorId: $actor->id,
                subject: $page,
                action: ActivityEntryAction::CmsPagePublished,
                description: __('activity.cms_page_published', ['page' => $page->slug]),
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
