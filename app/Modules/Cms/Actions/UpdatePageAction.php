<?php

namespace App\Modules\Cms\Actions;

use App\Models\Tenant\CmsPage;
use App\Models\Tenant\User;
use App\Modules\Audit\Enums\ActivityEntryAction;
use App\Modules\Audit\Services\ActivityLogger;
use App\Modules\Cms\Support\CmsContent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

final readonly class UpdatePageAction
{
    public function __construct(
        private ActivityLogger $activity,
        private CmsContent $cmsContent,
    ) {}

    /**
     * @param  array<string, mixed>  $content
     */
    public function handle(CmsPage $page, string $title, string $slug, array $content, User $actor): CmsPage
    {
        Gate::forUser($actor)->authorize('update', $page);

        $normalizedContent = $this->cmsContent->normalize($content);

        return DB::transaction(function () use ($page, $title, $slug, $normalizedContent, $actor): CmsPage {
            $before = $this->activityPayload($page);

            $page->forceFill([
                'title' => $title,
                'slug' => $slug,
                'draft_content' => $normalizedContent,
                'updated_by' => $actor->id,
            ])->save();

            $version = ((int) $page->revisions()->max('version')) + 1;

            $page->revisions()->create([
                'version' => $version,
                'title' => $page->title,
                'slug' => $page->slug,
                'status' => $page->status,
                'content' => $page->draft_content,
                'created_by' => $actor->id,
            ]);

            $this->activity->record(
                actorType: User::class,
                actorId: $actor->id,
                subject: $page,
                action: ActivityEntryAction::CmsPageUpdated,
                description: __('activity.cms_page_updated', ['page' => $page->slug]),
                before: $before,
                after: $this->activityPayload($page),
            );

            return $page->refresh();
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function activityPayload(CmsPage $page): array
    {
        return [
            'title' => $page->title,
            'slug' => $page->slug,
            'status' => $page->status->value,
            'content' => $page->draft_content,
        ];
    }
}
