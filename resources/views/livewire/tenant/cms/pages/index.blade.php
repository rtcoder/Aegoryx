<section class="grid gap-6 xl:grid-cols-[minmax(0,420px)_1fr]">
    <form wire:submit="save" class="ui-card space-y-4">
        <div class="ui-card-header">
            <h2 class="ui-heading-2">{{ $editingPageId ? __('cms.edit_page') : __('cms.new_page') }}</h2>
        </div>
        <div class="ui-card-body space-y-4">
            @if (session('success'))
                <div class="rounded border border-emerald-700 bg-emerald-950 px-4 py-3 text-sm text-emerald-100">
                    {{ session('success') }}
                </div>
            @endif

            <div>
                <label for="cms_title" class="ui-label">{{ __('cms.fields.title') }}</label>
                <input id="cms_title" wire:model="title" class="ui-input mt-2">
                @error('title') <p class="ui-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="cms_slug" class="ui-label">{{ __('cms.fields.slug') }}</label>
                <input id="cms_slug" wire:model="slug" class="ui-input mt-2">
                @error('slug') <p class="ui-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="cms_body" class="ui-label">{{ __('cms.fields.body') }}</label>
                <textarea id="cms_body" wire:model="body" rows="10" class="ui-textarea mt-2"></textarea>
                @error('body') <p class="ui-error">{{ $message }}</p> @enderror
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <x-ui.button type="submit">{{ __('cms.save') }}</x-ui.button>
                @if ($editingPageId)
                    <x-ui.button type="button" wire:click="cancel" variant="secondary">{{ __('cms.cancel') }}</x-ui.button>
                @endif
            </div>
        </div>
    </form>

    <div class="overflow-hidden rounded border border-neutral-800">
        <div class="border-b border-neutral-800 bg-neutral-900 px-4 py-4">
            <div class="flex flex-col gap-2 sm:flex-row">
                <input wire:model.live.debounce.400ms="search" class="ui-input min-w-64" placeholder="{{ __('common.search_placeholder') }}">
                @if (trim($search) !== '')
                    <x-ui.button type="button" wire:click="$set('search', '')" variant="ghost">{{ __('common.clear_search') }}</x-ui.button>
                @endif
            </div>
        </div>

        <table class="min-w-full divide-y divide-neutral-800 text-sm">
            <thead class="bg-neutral-900 text-left text-xs uppercase tracking-wide text-neutral-500">
                <tr>
                    <th class="px-4 py-3">{{ __('cms.fields.title') }}</th>
                    <th class="px-4 py-3">{{ __('cms.fields.slug') }}</th>
                    <th class="px-4 py-3">{{ __('cms.fields.status') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('cms.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-800 bg-neutral-950">
                @forelse ($pages as $page)
                    <tr>
                        <td class="px-4 py-3 font-medium text-neutral-100">{{ $page->title }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-neutral-500">{{ $page->slug }}</td>
                        <td class="px-4 py-3"><x-ui.badge>{{ $page->status->value }}</x-ui.badge></td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <x-ui.button type="button" wire:click="edit({{ $page->id }})" size="sm" variant="secondary">{{ __('cms.edit') }}</x-ui.button>
                                @if ($page->published_at)
                                    <x-ui.button type="button" wire:click="unpublish({{ $page->id }})" size="sm" variant="danger">{{ __('cms.unpublish') }}</x-ui.button>
                                @else
                                    <x-ui.button type="button" wire:click="publish({{ $page->id }})" size="sm">{{ __('cms.publish') }}</x-ui.button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-neutral-500">{{ trim($search) === '' ? __('cms.empty') : __('common.no_results') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
