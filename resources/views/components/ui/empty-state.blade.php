@props([
    'title',
    'actionLabel' => null,
    'actionHref' => null,
])

<div {{ $attributes->class('mx-auto flex max-w-md flex-col items-center justify-center gap-4 py-8 text-center') }}>
    <p class="text-sm text-[var(--ui-text-muted)]">{{ $title }}</p>

    @if ($actionLabel && $actionHref)
        <x-ui.button :href="$actionHref" size="sm">{{ $actionLabel }}</x-ui.button>
    @endif
</div>
