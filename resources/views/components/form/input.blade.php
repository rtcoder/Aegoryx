@props([
    'label',
    'name',
    'type' => 'text',
    'value' => null,
])

@php
    $id = $attributes->get('id', $name);
@endphp

<div>
    <label for="{{ $id }}" class="block text-sm font-medium text-neutral-300">{{ $label }}</label>
    <input
        id="{{ $id }}"
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ old($name, $value) }}"
        {{ $attributes->except('id')->class('mt-2 block w-full rounded border border-neutral-700 bg-neutral-950 px-3 py-2 text-neutral-100 outline-none focus:border-sky-400') }}
    >
    @error($name)
        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
    @enderror
</div>
