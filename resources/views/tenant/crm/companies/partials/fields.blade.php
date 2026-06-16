@php
    /** @var \App\Models\Tenant\CrmCompany|null $company */
    $company ??= null;
    $selectedContactIds = collect(old('contact_ids', $company?->contacts->pluck('id')->all() ?? []))
        ->map(fn ($id): int => (int) $id)
        ->all();
@endphp

<x-form.input name="name" :label="__('crm.company_name')" :value="$company?->name" />
<x-form.input name="website" type="url" :label="__('crm.website')" :value="$company?->website" />
<x-form.input name="email" type="email" :label="__('common.email')" :value="$company?->email" />
<x-form.input name="phone" :label="__('crm.phone')" :value="$company?->phone" />
<x-form.textarea name="notes" :label="__('crm.notes')" :value="$company?->notes" />

<div>
    <p class="ui-label">{{ __('crm.linked_contacts') }}</p>

    @if ($contacts === [])
        <p class="ui-help">{{ __('crm.no_contacts_for_company') }}</p>
    @else
        <div class="mt-2 space-y-2">
            @foreach ($contacts as $contactId => $contactName)
                <label class="flex items-center gap-3 rounded border border-[var(--ui-border)] bg-[var(--ui-surface-muted)] px-3 py-2">
                    <input
                        type="checkbox"
                        name="contact_ids[]"
                        value="{{ $contactId }}"
                        @checked(in_array((int) $contactId, $selectedContactIds, true))
                        class="rounded border-[var(--ui-border)] bg-[var(--ui-surface)] text-[var(--ui-accent)] focus:ring-[var(--ui-focus)]"
                    >
                    <span class="text-sm text-[var(--ui-text)]">{{ $contactName }}</span>
                </label>
            @endforeach
        </div>
    @endif

    @error('contact_ids')
        <p class="ui-error">{{ $message }}</p>
    @enderror

    @error('contact_ids.*')
        <p class="ui-error">{{ $message }}</p>
    @enderror
</div>
