@props([
    'sort',
    'currentSort' => request('sort'),
    'currentDirection' => request('direction', 'asc'),
    'query' => request()->query(),
])

@php
    $active = $currentSort === $sort;
    $direction = $currentDirection === 'desc' ? 'desc' : 'asc';
    $nextDirection = $active && $direction === 'asc' ? 'desc' : 'asc';
    $url = url()->current().'?'.http_build_query(array_merge($query, [
        'sort' => $sort,
        'direction' => $nextDirection,
        'page' => null,
    ]));
@endphp

<a href="{{ $url }}" {{ $attributes->class(['inline-flex items-center gap-2 hover:text-[var(--ui-text)]', 'text-[var(--ui-text)]' => $active]) }}>
    <span>{{ $slot }}</span>
    @if ($active)
        <span class="font-mono text-[10px] uppercase text-[var(--ui-text-muted)]">{{ $direction }}</span>
    @endif
</a>
