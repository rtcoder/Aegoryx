@props([
    'label',
    'name',
    'value' => null,
    'help' => null,
    'options' => [],
    'placeholder' => null,
])

@php
    $id = $attributes->get('id', $name);
    $selectedValue = old($name, $value);
@endphp

<div>
    <label for="{{ $id }}" class="ui-label">{{ $label }}</label>
    <select
        id="{{ $id }}"
        name="{{ $name }}"
        {{ $attributes->except('id')->class('ui-select') }}
    >
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected((string) $selectedValue === (string) $optionValue)>
                {{ $optionLabel }}
            </option>
        @endforeach

        @if ($slot->isNotEmpty())
            {{ $slot }}
        @endif
    </select>

    @if ($help)
        <p class="ui-help">{{ $help }}</p>
    @endif

    @error($name)
        <p class="ui-error">{{ $message }}</p>
    @enderror
</div>
