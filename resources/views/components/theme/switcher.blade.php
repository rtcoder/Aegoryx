@props([
    'theme' => 'light',
    'action' => null,
    'align' => 'end',
])

@php
    $theme = in_array($theme, ['light', 'dark'], true) ? $theme : 'light';
@endphp

<div
    data-theme-switcher
    @if ($action) data-theme-endpoint="{{ $action }}" @endif
    data-current-theme="{{ $theme }}"
    class="ui-theme-switcher"
    role="group"
    aria-label="{{ __('common.theme') }}"
>
    <button
        type="button"
        class="ui-theme-option"
        data-theme-value="light"
        aria-pressed="{{ $theme === 'light' ? 'true' : 'false' }}"
        title="{{ __('common.theme_light') }}"
    >
        <span aria-hidden="true">L</span>
        <span class="sr-only">{{ __('common.theme_light') }}</span>
    </button>
    <button
        type="button"
        class="ui-theme-option"
        data-theme-value="dark"
        aria-pressed="{{ $theme === 'dark' ? 'true' : 'false' }}"
        title="{{ __('common.theme_dark') }}"
    >
        <span aria-hidden="true">D</span>
        <span class="sr-only">{{ __('common.theme_dark') }}</span>
    </button>
</div>
