@props([
    'href' => null,
    'variant' => 'primary',
    'size' => 'md',
])

@php
    $variantClass = match ($variant) {
        'secondary' => 'ui-btn-secondary',
        'ghost' => 'ui-btn-ghost',
        'danger' => 'ui-btn-danger',
        default => 'ui-btn-primary',
    };

    $sizeClass = match ($size) {
        'sm' => 'ui-btn-sm',
        default => 'ui-btn-md',
    };

    $classes = trim("ui-btn {$variantClass} {$sizeClass}");
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => 'button'])->class($classes) }}>
        {{ $slot }}
    </button>
@endif
