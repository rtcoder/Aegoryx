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

    public ?int $editingPageId = null;

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

    public function render(): View
    {
        return view('livewire.tenant.cms.pages.index', [
            'pages' => CmsPage::query()
                ->latest('updated_at')
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
