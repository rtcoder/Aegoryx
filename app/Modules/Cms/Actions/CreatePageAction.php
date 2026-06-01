<?php

namespace App\Modules\Cms\Actions;

use App\Models\Tenant\CmsPage;
use App\Models\Tenant\User;
use App\Modules\Audit\Enums\ActivityEntryAction;
use App\Modules\Audit\Services\ActivityLogger;
use App\Modules\Cms\Enums\CmsPageStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final readonly class CreatePageAction
{
    public function __construct(
        private ActivityLogger $activity,
    ) {}

    /**
     * @param  array<string, mixed>  $content
     */
    public function handle(string $title, ?string $slug, array $content, User $actor): CmsPage
    {
        return DB::transaction(function () use ($title, $slug, $content, $actor): CmsPage {
            $page = CmsPage::query()->create([
                'title' => $title,
                'slug' => $slug ?: Str::slug($title),
                'status' => CmsPageStatus::Draft,
                'draft_content' => $content,
                'created_by' => $actor->id,
                'updated_by' => $actor->id,
            ]);

            $this->createRevision($page, $actor);

            $this->activity->record(
                actorType: User::class,
                actorId: $actor->id,
                subject: $page,
                action: ActivityEntryAction::CmsPageCreated,
                description: "CMS page [{$page->slug}] created.",
                after: $this->activityPayload($page),
            );

            return $page->refresh();
        });
    }

    private function createRevision(CmsPage $page, User $actor): void
    {
        $page->revisions()->create([
            'version' => 1,
            'title' => $page->title,
            'slug' => $page->slug,
            'status' => $page->status,
            'content' => $page->draft_content,
            'created_by' => $actor->id,
        ]);
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
