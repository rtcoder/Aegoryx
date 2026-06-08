@props([
    'label',
    'name',
    'checked' => false,
    'value' => '1',
    'help' => null,
])

@php
    $id = $attributes->get('id', $name);
@endphp

<div>
    <label for="{{ $id }}" class="flex items-start gap-3">
        <input
            id="{{ $id }}"
            name="{{ $name }}"
            type="checkbox"
            value="{{ $value }}"
            @checked(old($name, $checked))
            {{ $attributes->except('id')->class('mt-1 rounded border-[var(--ui-border)] bg-[var(--ui-surface-muted)] text-[var(--ui-accent)] focus:ring-[var(--ui-focus)]') }}
        >
        <span>
            <span class="ui-label">{{ $label }}</span>
            @if ($help)
                <span class="ui-help block">{{ $help }}</span>
            @endif
        </span>
    </label>

    @error($name)
        <p class="ui-error">{{ $message }}</p>
    @enderror
</div>
