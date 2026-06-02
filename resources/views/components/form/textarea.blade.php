@props([
    'label',
    'name',
    'rows' => 4,
    'value' => null,
])

@php
    $id = $attributes->get('id', $name);
@endphp

<div>
    <label for="{{ $id }}" class="block text-sm font-medium text-neutral-300">{{ $label }}</label>
    <textarea
        id="{{ $id }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        {{ $attributes->except('id')->class('mt-2 block w-full rounded border border-neutral-700 bg-neutral-950 px-3 py-2 text-neutral-100 outline-none focus:border-sky-400') }}
    >{{ old($name, $value) }}</textarea>
    @error($name)
        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
    @enderror
</div>
