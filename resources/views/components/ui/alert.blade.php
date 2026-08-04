@props([
    'variant' => 'info',
])

@php
    $variantClass = match ($variant) {
        'success' => 'ui-alert-success',
        'warning' => 'ui-alert-warning',
        'danger' => 'ui-alert-danger',
        default => 'ui-alert-info',
    };
@endphp

<div {{ $attributes->class("ui-alert {$variantClass}") }}>
    {{ $slot }}
</div>
