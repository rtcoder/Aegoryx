@php
    /** @var \App\Models\Tenant\CrmDeal|null $deal */
    $deal ??= null;
@endphp

<x-form.input name="title" :label="__('crm.deal_title')" :value="$deal?->title" />
<x-form.select name="company_id" :label="__('crm.company')" :value="$deal?->company_id" :options="$companies" :placeholder="__('crm.no_company')" />
<x-form.select name="contact_id" :label="__('crm.contact')" :value="$deal?->contact_id" :options="$contacts" :placeholder="__('crm.no_contact')" />
<x-form.select name="status" :label="__('common.status')" :value="$deal?->status->value ?? \App\Modules\Crm\Enums\CrmDealStatus::Open->value" :options="$statuses" />
<x-form.input name="value_amount" type="number" step="0.01" min="0" :label="__('crm.value_amount')" :value="$deal?->value_amount" />
<x-form.input name="currency" maxlength="3" :label="__('crm.currency')" :value="$deal?->currency" />
<x-form.input name="expected_close_date" type="date" :label="__('crm.expected_close_date')" :value="$deal?->expected_close_date?->toDateString()" />
<x-form.textarea name="notes" :label="__('crm.notes')" :value="$deal?->notes" />
