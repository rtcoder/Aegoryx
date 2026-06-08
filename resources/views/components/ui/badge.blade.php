@props([
    'variant' => 'neutral',
])

@php
    $variantClass = match ($variant) {
        'accent' => 'ui-badge-accent',
        'success' => 'ui-badge-success',
        'warning' => 'ui-badge-warning',
        'danger' => 'ui-badge-danger',
        default => 'ui-badge-neutral',
    };
@endphp

<span {{ $attributes->class("ui-badge {$variantClass}") }}>
    {{ $slot }}
</span>
