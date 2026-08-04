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
        aria-label="{{ __('common.theme_light') }}"
        title="{{ __('common.theme_light') }}"
    >
        <svg class="ui-theme-icon" aria-hidden="true" viewBox="0 0 24 24" fill="none">
            <path d="M12 4V2.75M12 21.25V20M17.66 6.34L18.55 5.45M5.45 18.55L6.34 17.66M20 12H21.25M2.75 12H4M17.66 17.66L18.55 18.55M5.45 5.45L6.34 6.34" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
            <circle cx="12" cy="12" r="4.25" stroke="currentColor" stroke-width="1.8" />
        </svg>
        <span class="ui-sr-only">{{ __('common.theme_light') }}</span>
    </button>
    <button
        type="button"
        class="ui-theme-option"
        data-theme-value="dark"
        aria-pressed="{{ $theme === 'dark' ? 'true' : 'false' }}"
        aria-label="{{ __('common.theme_dark') }}"
        title="{{ __('common.theme_dark') }}"
    >
        <svg class="ui-theme-icon" aria-hidden="true" viewBox="0 0 24 24" fill="none">
            <path d="M20.25 14.15A7.15 7.15 0 0 1 9.85 3.75 8.25 8.25 0 1 0 20.25 14.15Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        <span class="ui-sr-only">{{ __('common.theme_dark') }}</span>
    </button>
</div>
