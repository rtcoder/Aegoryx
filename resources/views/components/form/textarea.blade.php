@props([
    'label',
    'name',
    'rows' => 4,
    'value' => null,
    'help' => null,
])

@php
    $id = $attributes->get('id', $name);
@endphp

<div>
    <label for="{{ $id }}" class="ui-label">{{ $label }}</label>
    <textarea
        id="{{ $id }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        {{ $attributes->except('id')->class('ui-textarea') }}
    >{{ old($name, $value) }}</textarea>

    @if ($help)
        <p class="ui-help">{{ $help }}</p>
    @endif

    @error($name)
        <p class="ui-error">{{ $message }}</p>
    @enderror
</div>
