@props([
    'label',
    'name',
    'type' => 'text',
    'value' => null,
    'help' => null,
])

@php
    $id = $attributes->get('id', $name);
@endphp

<div>
    <label for="{{ $id }}" class="ui-label">{{ $label }}</label>
    <input
        id="{{ $id }}"
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ old($name, $value) }}"
        {{ $attributes->except('id')->class('ui-input') }}
    >

    @if ($help)
        <p class="ui-help">{{ $help }}</p>
    @endif

    @error($name)
        <p class="ui-error">{{ $message }}</p>
    @enderror
</div>
