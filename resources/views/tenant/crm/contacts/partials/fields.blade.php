@php
    $contact ??= null;
@endphp

<div class="grid gap-4 sm:grid-cols-2">
    <x-form.input name="first_name" :label="__('crm.first_name')" :value="$contact?->first_name" />
    <x-form.input name="last_name" :label="__('crm.last_name')" :value="$contact?->last_name" />
</div>

<x-form.input name="email" type="email" :label="__('common.email')" :value="$contact?->email" />
<x-form.input name="phone" :label="__('crm.phone')" :value="$contact?->phone" />
<x-form.input name="position" :label="__('crm.position')" :value="$contact?->position" />
<x-form.textarea name="notes" :label="__('crm.notes')" :value="$contact?->notes" />
