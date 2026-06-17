<?php

namespace App\Livewire\Tenant\Cms\Pages;

use App\Models\Tenant\CmsPage;
use App\Models\Tenant\User;
use App\Modules\Cms\Actions\CreatePageAction;
use App\Modules\Cms\Actions\PublishPageAction;
use App\Modules\Cms\Actions\UnpublishPageAction;
use App\Modules\Cms\Actions\UpdatePageAction;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class Index extends Component
{
    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('nullable|string|max:255')]
    public ?string $slug = null;

    #[Validate('required|string')]
    public string $body = '';

    #[Url(as: 'search')]
    public string $search = '';

    #[Url(as: 'sort')]
    public string $sort = 'updated_at';

    #[Url(as: 'direction')]
    public string $direction = 'desc';

    public ?int $editingPageId = null;

    public ?int $previewingPageId = null;

    public string $previewTitle = '';

    public string $previewBody = '';

    public function save(CreatePageAction $createPage, UpdatePageAction $updatePage): void
    {
        $this->validate();

        $actor = $this->user();

        if ($this->editingPageId === null) {
            $createPage->handle($this->title, $this->slug, ['body' => $this->body], $actor);
        } else {
            $page = CmsPage::query()->findOrFail($this->editingPageId);
            $updatePage->handle($page, $this->title, $this->slug ?: $page->slug, ['body' => $this->body], $actor);
        }

        $this->resetForm();
        session()->flash('success', __('cms.saved'));
    }

    public function edit(int $pageId): void
    {
        $page = CmsPage::query()->findOrFail($pageId);

        $this->editingPageId = $page->id;
        $this->title = $page->title;
        $this->slug = $page->slug;
        $this->body = (string) ($page->draft_content['blocks'][0]['data']['body'] ?? '');
    }

    public function publish(int $pageId, PublishPageAction $action): void
    {
        $action->handle(CmsPage::query()->findOrFail($pageId), $this->user());

        session()->flash('success', __('cms.published'));
    }

    public function unpublish(int $pageId, UnpublishPageAction $action): void
    {
        $action->handle(CmsPage::query()->findOrFail($pageId), $this->user());

        session()->flash('success', __('cms.unpublished'));
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    public function preview(int $pageId): void
    {
        $page = CmsPage::query()->findOrFail($pageId);

        $this->previewingPageId = $page->id;
        $this->previewTitle = $page->title;
        $this->previewBody = (string) ($page->draft_content['blocks'][0]['data']['body'] ?? '');
    }

    public function closePreview(): void
    {
        $this->reset(['previewingPageId', 'previewTitle', 'previewBody']);
    }

    public function sortBy(string $sort): void
    {
        if (! in_array($sort, ['title', 'slug', 'status', 'updated_at'], true)) {
            return;
        }

        if ($this->sort === $sort) {
            $this->direction = $this->direction === 'asc' ? 'desc' : 'asc';

            return;
        }

        $this->sort = $sort;
        $this->direction = 'asc';
    }

    public function render(): View
    {
        $search = trim($this->search);

        return view('livewire.tenant.cms.pages.index', [
            'pages' => CmsPage::query()
                ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                    $query
                        ->where('title', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                }))
                ->orderBy(
                    in_array($this->sort, ['title', 'slug', 'status', 'updated_at'], true) ? $this->sort : 'updated_at',
                    $this->direction === 'asc' ? 'asc' : 'desc',
                )
                ->get(),
        ]);
    }

    private function resetForm(): void
    {
        $this->reset(['title', 'slug', 'body', 'editingPageId']);
    }

    private function user(): User
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 403);

        return $user;
    }
}
