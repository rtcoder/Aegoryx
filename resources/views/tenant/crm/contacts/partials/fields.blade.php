@php
    $contact ??= null;
@endphp

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label for="first_name" class="block text-sm font-medium text-neutral-300">{{ __('crm.first_name') }}</label>
        <input id="first_name" name="first_name" value="{{ old('first_name', $contact?->first_name) }}" class="mt-2 block w-full rounded border border-neutral-700 bg-neutral-950 px-3 py-2 text-neutral-100 outline-none focus:border-sky-400">
        @error('first_name')<p class="mt-2 text-sm text-red-400">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="last_name" class="block text-sm font-medium text-neutral-300">{{ __('crm.last_name') }}</label>
        <input id="last_name" name="last_name" value="{{ old('last_name', $contact?->last_name) }}" class="mt-2 block w-full rounded border border-neutral-700 bg-neutral-950 px-3 py-2 text-neutral-100 outline-none focus:border-sky-400">
        @error('last_name')<p class="mt-2 text-sm text-red-400">{{ $message }}</p>@enderror
    </div>
</div>

<div>
    <label for="email" class="block text-sm font-medium text-neutral-300">{{ __('common.email') }}</label>
    <input id="email" name="email" type="email" value="{{ old('email', $contact?->email) }}" class="mt-2 block w-full rounded border border-neutral-700 bg-neutral-950 px-3 py-2 text-neutral-100 outline-none focus:border-sky-400">
    @error('email')<p class="mt-2 text-sm text-red-400">{{ $message }}</p>@enderror
</div>

<div>
    <label for="phone" class="block text-sm font-medium text-neutral-300">{{ __('crm.phone') }}</label>
    <input id="phone" name="phone" value="{{ old('phone', $contact?->phone) }}" class="mt-2 block w-full rounded border border-neutral-700 bg-neutral-950 px-3 py-2 text-neutral-100 outline-none focus:border-sky-400">
    @error('phone')<p class="mt-2 text-sm text-red-400">{{ $message }}</p>@enderror
</div>

<div>
    <label for="position" class="block text-sm font-medium text-neutral-300">{{ __('crm.position') }}</label>
    <input id="position" name="position" value="{{ old('position', $contact?->position) }}" class="mt-2 block w-full rounded border border-neutral-700 bg-neutral-950 px-3 py-2 text-neutral-100 outline-none focus:border-sky-400">
    @error('position')<p class="mt-2 text-sm text-red-400">{{ $message }}</p>@enderror
</div>

<div>
    <label for="notes" class="block text-sm font-medium text-neutral-300">{{ __('crm.notes') }}</label>
    <textarea id="notes" name="notes" rows="4" class="mt-2 block w-full rounded border border-neutral-700 bg-neutral-950 px-3 py-2 text-neutral-100 outline-none focus:border-sky-400">{{ old('notes', $contact?->notes) }}</textarea>
    @error('notes')<p class="mt-2 text-sm text-red-400">{{ $message }}</p>@enderror
</div>
