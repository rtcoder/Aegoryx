<?php

namespace Tests\Feature\Cms;

use App\Models\Tenant\ActivityEntry;
use App\Models\Tenant\CmsPage;
use App\Models\Tenant\CmsPageRevision;
use App\Models\Tenant\PublishedPage;
use App\Models\Tenant\User;
use App\Modules\Audit\Enums\ActivityEntryAction;
use App\Modules\Cms\Actions\CreatePageAction;
use App\Modules\Cms\Actions\PublishPageAction;
use App\Modules\Cms\Actions\UnpublishPageAction;
use App\Modules\Cms\Actions\UpdatePageAction;
use App\Modules\Cms\Enums\CmsPageStatus;
use App\Support\Localization\Locale;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

final class CmsPageWorkflowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh', [
            '--database' => 'sqlite',
            '--path' => 'database/migrations/tenant',
        ]);
    }

    public function test_create_page_creates_first_revision_and_activity(): void
    {
        $actor = $this->user();

        $page = app(CreatePageAction::class)->handle(
            title: 'About Us',
            slug: null,
            content: ['blocks' => [['type' => 'hero', 'secret' => 'do-not-store-in-activity']]],
            actor: $actor,
        );

        $this->assertSame('about-us', $page->slug);
        $this->assertSame(CmsPageStatus::Draft, $page->status);
        $this->assertSame($actor->id, $page->created_by);
        $this->assertSame(1, CmsPageRevision::query()->where('cms_page_id', $page->id)->count());

        $activity = ActivityEntry::query()->firstOrFail();

        $this->assertSame(ActivityEntryAction::CmsPageCreated, $activity->action);
        $this->assertSame(CmsPage::class, $activity->subject_type);
        $this->assertSame('[redacted]', $activity->after_json['content']['blocks'][0]['secret']);
    }

    public function test_update_page_creates_next_revision_and_activity(): void
    {
        $actor = $this->user();
        $page = app(CreatePageAction::class)->handle('About Us', null, ['body' => 'Draft'], $actor);

        $updated = app(UpdatePageAction::class)->handle(
            page: $page,
            title: 'About Aegoryx',
            slug: 'about-aegoryx',
            content: ['body' => 'Updated draft'],
            actor: $actor,
        );

        $this->assertSame('About Aegoryx', $updated->title);
        $this->assertSame('about-aegoryx', $updated->slug);
        $this->assertSame(2, CmsPageRevision::query()->where('cms_page_id', $page->id)->count());
        $this->assertSame(ActivityEntryAction::CmsPageUpdated, ActivityEntry::query()->latest('id')->firstOrFail()->action);
    }

    public function test_publish_creates_snapshot_and_activity(): void
    {
        $actor = $this->user();
        $page = app(CreatePageAction::class)->handle('Homepage', 'home', ['body' => 'Public body'], $actor);

        $published = app(PublishPageAction::class)->handle($page, $actor);

        $this->assertSame(CmsPageStatus::Published, $published->status);
        $this->assertNotNull($published->published_at);

        $snapshot = PublishedPage::query()->where('cms_page_id', $page->id)->firstOrFail();

        $this->assertSame('home', $snapshot->slug);
        $this->assertSame(['body' => 'Public body'], $snapshot->content);
        $this->assertSame(ActivityEntryAction::CmsPagePublished, ActivityEntry::query()->latest('id')->firstOrFail()->action);
    }

    public function test_unpublish_removes_snapshot_and_records_activity(): void
    {
        $actor = $this->user();
        $page = app(CreatePageAction::class)->handle('Homepage', 'home', ['body' => 'Public body'], $actor);

        app(PublishPageAction::class)->handle($page, $actor);
        app(UnpublishPageAction::class)->handle($page->refresh(), $actor);

        $this->assertSame(CmsPageStatus::Draft, $page->refresh()->status);
        $this->assertSame(0, PublishedPage::query()->where('cms_page_id', $page->id)->count());
        $this->assertSame(ActivityEntryAction::CmsPageUnpublished, ActivityEntry::query()->latest('id')->firstOrFail()->action);
    }

    private function user(): User
    {
        return User::query()->create([
            'name' => 'Tenant Editor',
            'email' => 'editor@example.test',
            'password' => 'secret-password',
            'locale' => Locale::Polish,
        ]);
    }
}
